<?php

error_reporting(-1);
session_start();

$debug = false;

if (ob_get_level() == 0) {
	ob_start();
}

require 'vendor/autoload.php';

use Guzzle\Http\Client;
use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;

if ( isset( $_GET['reset'] ) ) {
	$_SESSION['handled'] = [];
	header('Location: app.php');
	exit();
}

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

$ledger_id_omzet = '126847062986720562';

$invoices = $handler->get( 'sales_invoices/synchronization.json' );

if( ! is_array( $invoices ) ) {
	die( 'Error getting all invoices.' );
}

echo '<pre>';
var_dump( $invoices );
echo '</pre>';

exit();


$handled_invoices = isset( $_SESSION['handled'] ) ? $_SESSION['handled'] : '';
if( empty( $handled_invoices ) ) {
	$handled_invoices = [];
}

echo '<pre>';
var_dump( $handled_invoices );
echo '</pre>';

$counter = 0;

foreach ( $invoices as $i => $invoice ) {
	if( in_array( $invoice['id'], $handled_invoices ) ) {
		continue;
	}

	$invoice_url = 'sales_invoices/' . $invoice['id'] . '.json';
	$invoice = $handler->get( $invoice_url );

	if( false == $invoice ) {
		break;
	}

	$invoice_amount = $invoice['total_price_incl_tax'];
	if( $invoice_amount > 100 ) {
		continue;
	}

	if( $counter >= 50 ) {
		break;
	}

 	$args = [];

	foreach ( $invoice['details'] as $key => $detail ) {
		$args[ 'sales_invoice']['details_attributes'][ $key ] = $detail;
		$args[ 'sales_invoice']['details_attributes'][ $key ]['ledger_account_id'] = $ledger_id_omzet;
	}

	echo '<strong>' . $counter . ': Invoice #' . $invoice['invoice_id'] . '</strong><br>';
	foreach ( $invoice['details'] as $key => $detail ) {
		echo $detail['amount'] . 'x ' . $detail['description'] . '(€' . number_format( (float)$detail['price'], 2, '.', ',' )  . ')' . '<br>';
	}
	echo 'Totaal: € ' . number_format( (float)$invoice_amount, 2, '.', ',' ) . '<br>';ob_flush(); flush();

	if( false == $debug ) {
		$output = $handler->patch( $invoice_url, $args );
		if ( ! is_array( $output ) ) {
			echo '<strong>ERROR!</strong><br>';
			echo 'URL: ' . $invoice_url . '<br>';
			echo 'Input: ' . json_encode( $args, JSON_FORCE_OBJECT ) . '<br>';
			echo $output . '<br><hr><br>';
		}
		else {
			echo '<strong>Updated.</strong><br>';
		}
	}

	echo '<br><hr><br>'; ob_flush(); flush();

	$handled_invoices[] = $invoice['id'];
	$_SESSION['handled'] = $handled_invoices;
	$counter++;
}


ob_end_flush();