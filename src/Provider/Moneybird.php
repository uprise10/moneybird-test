<?php

namespace Uprise\OAuth2\Client\Moneybird\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class Moneybird extends AbstractProvider {

	public function urlAuthorize() {
		return 'https://moneybird.com/oauth/authorize';
	}

	public function urlAccessToken() {
		return 'https://moneybird.com/oauth/token';
	}

	public function urlUserDetails( AccessToken $token) {

	}

	public function userDetails($response, AccessToken $token) {

	}

}