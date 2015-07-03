<?php

session_start();

error_reporting(-1);
require 'vendor/autoload.php';

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
	$_SESSION['oauth2state'] = $provider->state;

	header('Location: '.$authUrl);

	exit;
}
else {
	$token = $provider->getAccessToken('authorization_code', [
		'code' => $_GET['code']
	]);

	// Use this to interact with an API on the users behalf
	$token = $token->accessToken;
	$_SESSION['moneybird_token'] = $token;

	header('Location: app.php');
}
