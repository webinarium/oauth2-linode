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

use Linode\OAuth2\Client\Provider\LinodeResourceOwner;
use PHPUnit\Framework\TestCase;

class LinodeResourceOwnerTest extends TestCase
{
    /** @var LinodeResourceOwner */
    protected $owner;

    protected function setUp()
    {
        $this->owner = new LinodeResourceOwner([
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
        ]);
    }

    public function testGetId()
    {
        self::assertNull($this->owner->getId());
    }

    public function testToArray()
    {
        self::assertCount(14, $this->owner->toArray());
    }

    public function testGetFirstName()
    {
        self::assertSame('Sherlock', $this->owner->getFirstName());
    }

    public function testGetLastName()
    {
        self::assertSame('Holmes', $this->owner->getLastName());
    }

    public function testGetEmail()
    {
        self::assertSame('holmes@example.com', $this->owner->getEmail());
    }

    public function testGetCompany()
    {
        self::assertSame('Consulting Detective 1881-1904', $this->owner->getCompany());
    }

    public function testGetAddress()
    {
        $address = [
            'address_1' => '221B Baker Street',
            'address_2' => null,
            'city'      => 'London',
            'state'     => null,
            'country'   => 'GB',
            'zip'       => 'NW1 6XE',
        ];
        self::assertSame($address, $this->owner->getAddress());
    }

    public function testGetPhone()
    {
        self::assertSame('01+44+207 224 3688', $this->owner->getPhone());
    }

    public function testGetTaxId()
    {
        self::assertNull($this->owner->getTaxId());
    }

    public function testGetBalance()
    {
        self::assertSame('0', $this->owner->getBalance());
    }

    public function testGetCreditCard()
    {
        $creditCard = [
            'last_four' => '1234',
            'expiry'    => '07/1905',
        ];

        self::assertSame($creditCard, $this->owner->getCreditCard());
    }
}
