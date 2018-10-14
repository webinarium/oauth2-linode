<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  You should have received a copy of the MIT License along with
//  this file. If not, see <http://opensource.org/licenses/MIT>.
//
//----------------------------------------------------------------------

namespace Linode\OAuth2\Client\Provider\Tests;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Linode\OAuth2\Client\Provider\Linode;
use Linode\OAuth2\Client\Provider\LinodeResourceOwner;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class LinodeTest extends TestCase
{
    /** @var Linode */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Linode();
    }

    public function testGetBaseAuthorizationUrl()
    {
        self::assertSame('https://login.linode.com/oauth/authorize', $this->provider->getBaseAuthorizationUrl());
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];

        self::assertSame('https://login.linode.com/oauth/token', $this->provider->getBaseAccessTokenUrl($params));
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $token = new AccessToken([
            'access_token' => '423729',
        ]);

        self::assertSame('https://api.linode.com/v4/account', $this->provider->getResourceOwnerDetailsUrl($token));
    }

    public function testGetDefaultScopes()
    {
        self::assertSame([], $this->callMethod('getDefaultScopes'));
    }

    public function testCheckResponseSuccess()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $data = [
            'access_token' => '423729',
        ];

        $this->callMethod('checkResponse', [$response, $data]);
    }

    public function testCheckResponseFailure()
    {
        $this->expectException(IdentityProviderException::class);
        $this->expectExceptionMessage('Bad credentials');

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(401);

        $data = [
            'errors' => [
                ['reason' => 'Bad credentials'],
                ['reason' => 'Missing code'],
                ['reason' => 'Wrong state'],
            ],
        ];

        $this->callMethod('checkResponse', [$response, $data]);
    }

    public function testCreateResourceOwner()
    {
        $response = [
            'first_name'  => 'Sherlock',
            'last_name'   => 'Holmes',
            'email'       => 'holmes@example.com',
            'company'     => 'Consulting Detective 1881-1904',
            'address_1'   => '221B Baker Street',
            'address_2'   => null,
            'city'        => 'London',
            'state'       => null,
            'country'     => 'GB',
            'zip'         => 'NW1 6XE',
            'phone'       => '01+44+207 224 3688',
            'tax_id'      => null,
            'balance'     => '0',
            'credit_card' => [
                'last_four' => '1234',
                'expiry'    => '07/1905',
            ],
        ];

        $token = new AccessToken([
            'access_token' => '423729',
        ]);

        $owner = $this->callMethod('createResourceOwner', [$response, $token]);

        self::assertInstanceOf(LinodeResourceOwner::class, $owner);
    }

    protected function callMethod($name, array $args = [])
    {
        try {
            $reflection = new \ReflectionMethod(get_class($this->provider), $name);
            $reflection->setAccessible(true);

            return $reflection->invokeArgs($this->provider, $args);
        }
        catch (\ReflectionException $e) {
            return null;
        }
    }
}
