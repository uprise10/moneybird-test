<?php

namespace Uprise\OAuth2\Client\Moneybird\Provider;

use Guzzle\Http\Client;
use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;

class Handler {

	var $base_url = '';
	var $token = '';


	function get( $endpoint, $data = array() ) {

		try {
			$client = new Client();
			$bearerAuth = new BearerAuth( $this->token );
			$client->addSubscriber($bearerAuth);

			$request = $client->get( $this->base_url . $endpoint );
			$request->addHeader('Content-Type', 'application/json' );
			$response = $request->send();

			return $response->json();

		}
		catch ( BearerErrorResponseException $e ) {
			echo '<pre>';
			var_dump( $e->getMessage() );
			echo '</pre>';
			return false;
		}
		catch ( BadResponseException $e ) {
			echo '<pre>';
			var_dump( $e->getMessage() );
			echo '</pre>';
			return false;
		}

	}

	function patch( $endpoint, $data = array() ) {

		try {
			$client = new Client();
			$bearerAuth = new BearerAuth( $this->token );
			$client->addSubscriber($bearerAuth);

			$request = $client->patch( $this->base_url . $endpoint, array(), json_encode( $data, JSON_FORCE_OBJECT ) );
			$request->addHeader('Content-Type', 'application/json' );
			$response = $request->send();

			return $response->json();

		}
		catch ( BearerErrorResponseException $e ) {
			return $e->getMessage();
		}
		catch ( BadResponseException $e ) {
			return $e->getMessage();
		}

	}



	function set_base_url( $base_url ) {
		$this->base_url = $base_url;
	}

	function set_token( $token ) {
		$this->token = $token;
	}

}