<?php

session_start();

error_reporting(-1);
require 'vendor/autoload.php';

use Guzzle\Http\Client;
use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;


$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

echo '<h1>Moneybird API test</h1>';

$token = $_SESSION['moneybird_token'];


$base_url = 'https://moneybird.com/api/v2/' . getenv( 'MONEYBIRD_ADMINISTRATION_ID' ) . '/';
$url = $base_url . 'sales_invoices/synchronization.json';

$exec = 'curl -XGET -H "Content-Type: application/json" -H "Authorization: Bearer ' . $token . '"  -d \'{"filter":"period:this_month"}\' ' . $url;
echo '<h3>Via Terminal</h3>';
echo '<strong>Request</strong><br>';
echo '<code>' . $exec . '</code><br>';

exec( $exec, $output );
echo '<strong>Output:</strong><br><pre>';
var_dump( $output );
echo '</pre>';

echo '<h3>Via Guzzle</h3>';
echo 'Request URL: ' . $url . '<br>';

try {
	$client = new Client();
	$bearerAuth = new BearerAuth( $token );
	$client->addSubscriber($bearerAuth);

	$request = $client->get( $url );
	$request->addHeader('Content-Type', 'application/json' );
	$response = $request->send();

	echo '<pre>';
	var_dump( $response->getStatusCode(), $response->getBody() );
	echo '</pre>';
}
catch ( BearerErrorResponseException $e ) {
	echo '<pre>';
	var_dump( $e->getMessage() );
	echo '</pre>';
}
catch ( BadResponseException $e ) {
	echo '<pre>';
	var_dump( $e->getMessage() );
	echo '</pre>';
}
