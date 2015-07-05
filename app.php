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

echo '<strong>Request URL:</strong> <code>' . $url . '</code>';

try {
	$client = new Client();
	$bearerAuth = new BearerAuth( $token );
	$client->addSubscriber($bearerAuth);

	$options = array(
		'filter', 'period:this_month'
	);

	$request = $client->get( $url );
	$request->addHeader('Content-Type', 'application/json' );

	// Add filter to the query
//	$query = $request->getQuery();
//	$query->add( 'filter', 'period:last_month' );
//	$query->useUrlEncoding( false );

	$response = $request->send();

	$result = $response->json();
	echo '# results: ' . count( $result ) . '<br>';
	echo '<pre>';
	print_r( $result );
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
