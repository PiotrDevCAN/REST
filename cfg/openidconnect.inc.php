<?php
$config_openidconnect = new stdClass();
$config_openidconnect->authorize_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/authorize";
$config_openidconnect->token_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/token";
$config_openidconnect->introspect_url = "https://w3id.alpha.sso.ibm.com/isam/oidc/endpoint/amapp-runtime-oidcidp/introspect";

$config_openidconnect->client_id['rest'] = "MTYxNjY3ODYtODk5ZS00";
$config_openidconnect->client_secret['rest'] = "MTA1Y2I1MGItNjAzZC00";
$config_openidconnect->redirect_url['rest'] = "https://rest.w3ibm.mybluemix.net/auth/index.php";

$config_openidconnect->client_id['rest_dev'] = "ZGE0NDYzMTctYmZhNS00";
$config_openidconnect->client_secret['rest_dev'] = "ZWFhM2U5MTktNDk4NS00";
$config_openidconnect->redirect_url['rest_dev'] = "https://restdev.w3ibm.mybluemix.net/auth/index.php";

$config_openidconnect->client_id['rest_xt'] = "NjRiYmY1NjYtZjhlYi00";
$config_openidconnect->client_secret['rest_xt'] = "YjZkMmUyMmMtNmViMS00";
$config_openidconnect->redirect_url['rest_xt'] = "https://rest-dev-x.w3ibm.mybluemix.net/auth/index.php";


?>