<?php
namespace itdq;

use WorkerApi\Auth;

include_once "WorkerAPI/class/include.php";

/*
 *  Handles OKTA Groups.
 */
class OKTAGroups {

	private $token = null;
	private $hostname = null;

	private $redis = null;

	public function __construct()
	{
		$auth = new Auth();
		$auth->ensureAuthorized();

		$this->hostname = trim($_ENV['sso_host']);
		$this->token = trim($_ENV['sso_api_token']);
		
		$this->redis = $GLOBALS['redis'];
	}

	private function createCurl($type = "GET")
	{
		// create a new cURL resource
		$ch = curl_init();
		$authorization = "Authorization: SSWS ".$this->token; // Prepare the authorisation token
		$headers = [
			'Content-Type: application/json',
			'Accept: application/json, text/json, application/xml, text/xml',
			$authorization,
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		return $ch;
	}

	private function processURL($url, $type = 'GET')
	{
		$url = $this->hostname . $url;
		$ch = $this->createCurl($type);
		// echo "<BR>Processing";
		// echo " URL:" . $url;
		$ret = curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		$info = curl_getinfo($ch);

		$result = json_decode($ret, true);

		if (empty($ret)) {
			// some kind of an error happened
			// die(curl_error($ch));
			curl_close($ch); // close cURL handler
		} else {
			$info = curl_getinfo($ch);
			if (empty($info['http_code'])) {
				// die("No HTTP code was returned");
			} else if ($info['http_code'] == 500) {
				echo $result['message'];
			} else {
				// So Bluegroups has processed our URL - What was the result.
				$bgapiRC  = substr($ret,0,1);
				if($bgapiRC!=0){
					// Bluegroups has NOT returned a ZERO - so there was a problem
					echo "<H3>Error processing Bluegroup URL </H3>";
					echo "<H2>Please take a screen print of this page and send to the ITDQ Team ASAP.</H2>";
					echo "<BR>URL<BR>";
					print_r($url);
					echo "<BR>Info<BR>";
					print_r($info);
					echo "<BR>";
					exit ("<B>Unsuccessful RC: $ret</B>");
				} else {
					// echo " Successful RC: $ret";
					sleep(1); // Give BG a chance to process the request.
				}
			}
		}
		return $result;
	}

	/*
	* Group operations
	*/

	public function addGroup($groupName, $description)
	{
		$profile = [
			"name" => $groupName,
			"description" => $description
		];
		$url = "/api/v1/groups";
		return $this->processURL($url, 'POST', $profile);
	}

	public function getGroup($groupId)
	{
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'GET');
	}

	public function listGroups()
	{
		$url = "/api/v1/groups";
		return $this->processURL($url, 'GET');
	}

	public function getGroupByName($groupName, $limit = 10)
	{
		$groupName = urlencode($groupName);
		$url = "/api/v1/groups?q=$groupName&limit=$limit";
		return $this->processURL($url, 'GET');
	}

	public function updateGroup($groupId, $groupName, $description)
	{
		$profile = [
			"name" => $groupName,
			"description" => $description
		];
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'PUT', $profile);
	}

	public function removeGroup($groupId)
	{
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'DELETE');
	}

	/*
	* Group member operations
	*/

	public function listMembers($groupId)
	{
		$url = "/api/v1/groups/$groupId/users";
		return $this->processURL($url, 'GET');
	}

	public function addMember($groupId, $userId)
	{
		$url = "/api/v1/groups/$groupId/users/$userId";
		return $this->processURL($url, 'PUT');
	}
	
	public function removeMember($groupId, $userId)
	{
		$url = "/api/v1/groups/$groupId/users/$userId";
		return $this->processURL($url, 'DELETE');
	}

	/*
	* Group role operations
	*/

	public function createGroupRole()
	{
		$url = "/api/v1/groups/rules";
		return $this->processURL($url, 'POST');		
	}

	public function updateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'PUT');
	}

	public function listGroupRules()
	{
		$url = "/api/v1/groups/rules";
		return $this->processURL($url, 'GET');
	}

	public function getGroupRule($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'GET');
	}

	public function removeGroupRule($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'DELETE');
	}

	public function activateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId/lifecycle/activate";
		return $this->processURL($url, 'POST');
	}

	public function deactivateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId/lifecycle/deactivate";
		return $this->processURL($url, 'POST');
	}

	/*
	* Auxiliary operations
 	*/

	public function getGroupMembersKey($groupName)
	{
		return md5($groupName.'_members');
	}

	public function getGroupMembers($groupName)
	{
		$redisKey = $this->getGroupMembersKey($groupName);
		$this->redis->expire($redisKey, REDIS_EXPIRE);
		if (!$this->redis->get($redisKey)) {
			$source = 'SQL Server';

			$groupId = $this->getGroupId($groupName);
			$result = $this->listMembers($groupId);

			$this->redis->set($redisKey, json_encode($result));
			$this->redis->expire($redisKey, REDIS_EXPIRE);
		} else {
			$source = 'Redis Server';
			$result = json_decode($this->redis->get($redisKey), true);
		}
		return $result;
	}

	public function clearGroupMembersCache($groupName)
	{
		$redisKey = $this->getGroupMembersKey($groupName);
		$this->redis->del($redisKey);
	}

	public function inAGroup($groupName, $ssoEmail)
	{
		$users = $this->getGroupMembers($groupName);

		$found = false;
		foreach($users as $key => $row) {
			$email = $row['profile']['email'];
			if (strtolower(trim($email)) == strtolower(trim($ssoEmail))) {
				$found = true;
			}
		}
		return $found;
	}

	public function getGroupId($groupName)
	{
		$key = $groupName.'_key';
		$redisKey = md5($key.'_key_'.$_ENV['environment']);
		if (!$this->redis->get($redisKey)) {
			$source = 'SQL Server';

			$result = $this->getGroupByName($groupName);

			$this->redis->set($redisKey, json_encode($result));
			$this->redis->expire($redisKey, REDIS_EXPIRE);
		} else {
			$source = 'Redis Server';
			$result = json_decode($this->redis->get($redisKey), true);
		}

		$groupId = false;
		foreach($result as $key => $row) {
			if ($row['profile']['name'] == $groupName) {
				$groupId = 	$row['id'];
			}
		}
		return $groupId;
	}

	public function getGroupName($groupId)
	{
		$key = $groupId.'_getGroupName_key';
		$redisKey = md5($key.'_key_'.$_ENV['environment']);
		if (!$this->redis->get($redisKey)) {
			$source = 'SQL Server';

			$result = $this->getGroup($groupId);

			$this->redis->set($redisKey, json_encode($result));
			$this->redis->expire($redisKey, REDIS_EXPIRE);
		} else {
			$source = 'Redis Server';
			$result = json_decode($this->redis->get($redisKey), true);
		}

		$groupName = $result['profile']['name'];
		return $groupName;
	}
}
?>