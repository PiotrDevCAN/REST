<?php
$config_openidconnect = new stdClass();

/*
 * SSO Element of Config
 *
 */

$config_openidconnect->client_id      = $_ENV['worker_api_client_id'];
$config_openidconnect->client_secret  = $_ENV['worker_api_client_secret'];
$config_openidconnect->token_scope    = $_ENV['worker_api_token_scope'];

// Token scope
// DEV: api://51d4cf5d-b248-4e4f-b6dd-5897e73e247f/.default
// TEST: api://61ed81e4-e465-4921-a2f9-dfbca0f0eae0/.default
// PROD: api://b1bd8450-c0b1-46d9-99d2-52a55d58c8d2/.default

$config_openidconnect->authorize_url  = $_ENV['worker_api_authority'].'/authorize';
$config_openidconnect->token_url      = $_ENV['worker_api_authority'].'/token';
$config_openidconnect->userinfo_url   = $_ENV['worker_api_authority'].'/userinfo';
$config_openidconnect->introspect_url = $_ENV['worker_api_authority'].'/introspect';
 
error_log('Authorising to:' . $config_openidconnect->authorize_url . " as (" . $config_openidconnect->client_id . ") ");

/*
 * Application Instance of Config
 *
 */

$config_openidconnect->redirect_url = "https://" . $_SERVER['HTTP_HOST'] . "/auth/index.php";

?>