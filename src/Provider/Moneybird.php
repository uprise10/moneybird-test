<?php

namespace Uprise\OAuth2\Client\Moneybird\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Moneybird extends AbstractProvider {

    public function urlAuthorize() {
        return 'https://moneybird.com/oauth/authorize';
    }

    public function urlAccessToken() {
        return 'https://moneybird.com/oauth/token';
    }

    public function getBaseAuthorizationUrl() {
        return 'https://moneybird.com/oauth/authorize';
    }

    public function urlUserDetails( AccessToken $token ) {

    }

    public function userDetails( $response, AccessToken $token ) {

    }

    public function getBaseAccessTokenUrl( array $params ) {
        return 'https://moneybird.com/oauth/token';
    }

    public function getResourceOwnerDetailsUrl( AccessToken $token ) {

    }

    public function getDefaultScopes() {

    }

    public function checkResponse( ResponseInterface $response, $data ) {

    }

    public function createResourceOwner( array $response, AccessToken $token ) {

    }
}