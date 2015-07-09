<?php

session_start();

error_reporting(-1);
require 'vendor/autoload.php';

use Guzzle\Http\Client;
use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;

// Load .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$base_url = 'https://moneybird.com/api/v2/' . getenv( 'MONEYBIRD_ADMINISTRATION_ID' ) . '/';
$token = $_SESSION['moneybird_token'];


echo '<h1>Moneybird API test</h1>';

// Get all invoices
$handler = new Uprise\OAuth2\Client\Moneybird\Provider\Handler();
$handler->set_base_url( $base_url );
$handler->set_token( $token );

$ledger_accounts = $handler->get( 'ledger_accounts.json' );
$ledger_id_omzet = '126847062986720562';

$invoices = $handler->get( 'sales_invoices/synchronization.json' );

if( ! is_array( $invoices ) ) {
	die( 'Error getting all invoices.' );
}
echo '<pre>';
var_dump( $token );
echo '</pre>';

foreach ( $invoices as $i => $invoice ) {
	$invoice_url = 'sales_invoices/' . $invoice['id'] . '.json';
	$invoice = $handler->get( $invoice_url );

	if( false == $invoice ) {
		continue;
	}

	if( $i > 0 ) {
		break;
	}

	echo '<pre>';
	var_dump( $invoice );
	echo '</pre>';

 	$args = [];

	foreach ( $invoice['details'] as $key => $detail ) {
		$args[ 'sales_invoice']['details_attributes'][ $key ]['id'] = $detail['id'];
		$args[ 'sales_invoice']['details_attributes'][ $key ]['ledger_account_id'] = $ledger_id_omzet;
	}

echo '<pre>';
print_r( $args );
echo '</pre>';
	echo '<br>' . json_encode( $args, JSON_FORCE_OBJECT ) . '<br>';

	$output = $handler->patch( $invoice_url, $args );
	echo '<pre>';
	print_r( $output );
	echo '</pre>';


}
