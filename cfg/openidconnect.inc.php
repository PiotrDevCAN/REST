<?php
$config_openidconnect = new stdClass();

/*
 * SSO Element of Config
 *
 */

$config_openidconnect->client_id      = $_ENV['sso_client_id'];
$config_openidconnect->client_secret  = $_ENV['sso_client_secret'];

$config_openidconnect->authorize_url  = $_ENV['sso_authorize_url'];
$config_openidconnect->token_url      = $_ENV['sso_token_url'];
$config_openidconnect->introspect_url = $_ENV['sso_introspect_url'];

error_log('Authorising to:' . $config_openidconnect->authorize_url . " as (" . $config_openidconnect->client_id . ") ");


/*
 * Application Instance of Config
 *
 */

$config_openidconnect->redirect_url = "https://" . $_SERVER['HTTP_HOST'] . "/auth/index.php";




?>