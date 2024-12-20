<?php

namespace SocialiteProviders\Imgur;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'IMGUR';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://api.imgur.com/oauth2/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://api.imgur.com/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.imgur.com/3/account/me',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );
        $response2 = $this->getHttpClient()->get(
            'https://api.imgur.com/3/account/me/settings',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        return array_merge(
            json_decode((string) $response->getBody(), true)['data'],
            json_decode((string) $response2->getBody(), true)['data']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['url'],
            'name'     => $user['url'],
            'email'    => $user['email'],
            'avatar'   => null,
        ]);
    }
}
