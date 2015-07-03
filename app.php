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
$url = $base_url . 'contacts.json';

$exec = 'curl -XGET -H "Content-Type: application/json" -H "Authorization: Bearer ' . $token . '" ' . $url;
echo '<h3>Via Terminal</h3>';
echo '<strong>Request</strong><br>';
echo '<code>' . $exec . '</code><br>';

exec( $exec, $output );
echo '<strong>Output:</strong><br><pre>';
var_dump( $output );
echo '</pre>';

echo '<h3>Via Guzzle</h3>';
echo 'Request URL: ' . $url . '<br>';
echo '<pre>';
echo '$client = new Client();
$request = $client->get( $url );
$request->addHeader(\'Content-Type\', \'application/json\' );
$request->addHeader(\'Authorization\', \'Bearer \' . $token );';
echo '</pre>';

try {
	$client = new Client();
	$bearerAuth = new BearerAuth("12345");
	$client->addSubscriber($bearerAuth);

	$request = $client->get( $url );
	$request->addHeader('Content-Type', 'application/json' );
	$response = $request->send();

	echo '<pre>';
	var_dump( $response );
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


// set URL and other appropriate options
$ch = curl_init();
$headers = array(
	'Content-Type: application/json',
	'Authorization: Bearer ' . $token
);

echo '<h3>Via PHP CURL</h3>';
echo '<strong>Request:</strong><br>';
echo 'URL: ' . $url . '<br>';
echo '<pre>';
print_r( $headers );
echo '</pre>';

curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_HEADER, $headers);

$response["response"]      = curl_exec($ch);
$response["error"]         = curl_errno($ch);
$response["error_message"] = curl_error($ch) ;
$response["info"]          = curl_getinfo($ch);

echo '<strong>Output:</strong><br>';
echo '<pre>';
var_dump( $response );
echo '</pre>';


// close cURL resource, and free up system resources
curl_close($ch);