<?php
$config_openidconnect = new stdClass();

/*
 * SSO Element of Config
 *
 */

$config_openidconnect->client_id['staging']      = isset($_ENV['SSO_staging_client_id']) ?  $_ENV['SSO_staging_client_id'] : null;
$config_openidconnect->client_secret['staging']  = isset($_ENV['SSO_staging_client_secret']) ? $_ENV['SSO_staging_client_secret'] : null;

$config_openidconnect->authorize_url['staging']  = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url['staging']      = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url['staging'] = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

$config_openidconnect->client_id['production']      = isset($_ENV['SSO_production_client_id']) ?  $_ENV['SSO_production_client_id'] : null;
$config_openidconnect->client_secret['production']  = isset($_ENV['SSO_production_client_secret']) ? $_ENV['SSO_production_client_secret'] : null;

$config_openidconnect->authorize_url['production']  = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url['production']      = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url['production'] = "https://w3id.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

/*
 * Application Instance of Config
 *
 */

$config_openidconnect->redirect_url = "https://" . $_SERVER['HTTP_HOST'] . "/auth/index.php";




?>