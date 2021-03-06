<?php

session_start();

error_reporting(-1);
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Redirect to app when token is already retrieved
if( isset( $_SESSION['moneybird_token'] ) ) {
	header( 'Location: app.php' );
	exit;
}

$provider = new Uprise\OAuth2\Client\Moneybird\Provider\Moneybird([
	'clientId'      => getenv( 'MONEYBIRD_CLIENT_ID' ),
	'clientSecret'  => getenv( 'MONEYBIRD_CLIENT_SECRET' ),
	'redirectUri'   => 'http://localhost/moneybird',
	'scopes'        => ['sales_invoices'],
]);

if( ! isset( $_GET['code'] ) ) {
	$authUrl = $provider->getAuthorizationUrl();
	$_SESSION['oauth2state'] = $provider->getState();

	header('Location: '.$authUrl);

	exit;
}
else {
	$token = $provider->getAccessToken('authorization_code', [
		'code' => $_GET['code']
	]);

	// Use this to interact with an API on the users behalf
	$_SESSION['moneybird_token'] = $token->getToken();

	header('Location: app.php');
}
