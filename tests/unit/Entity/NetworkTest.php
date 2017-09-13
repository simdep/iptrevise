<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 *
 */

namespace App\Tests\Entity;

use App\Entity\Network;
use App\Entity\Ip;
use PHPUnit\Framework\TestCase;

/**
 * Entity network unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class NetworkTest extends TestCase
{
    /**
     * @var Network
     */
    private $network;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->network = new Network();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertNull($this->network->getCreated());
        self::assertNull($this->network->getId());
        self::assertNull($this->network->getColor());
        self::assertNull($this->network->getLabel());
        self::assertNull($this->network->getDescription());
        self::assertNull($this->network->getIp());
        self::assertNull($this->network->getCidr());
        self::assertNotNull($this->network->getIps());
        self::assertEmpty($this->network->getIps());
        self::assertNull($this->network->getUpdated());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        $expected = $actual = 'label';
        
        self::assertEquals($this->network, $this->network->setLabel($actual));
        self::assertEquals($expected, $this->network->getLabel());
    }

    /**
     * Tests description getter, setter and aliases.
     */
    public function testDescription()
    {
        $expected = $actual = 'description';
        
        self::assertEquals($this->network, $this->network->setDescription($actual));
        self::assertEquals($expected, $this->network->getDescription());
    }

    /**
     * Tests ip getter, setter and aliases.
     */
    public function testIp()
    {
        $expected = $actual = ip2long('255.255.255.250');
        
        self::assertEquals($this->network, $this->network->setIp($actual));
        self::assertEquals($expected, $this->network->getIp());
    }

    /**
     * Tests cidr getter, setter and aliases.
     */
    public function testCidr()
    {
        $expected = $actual = 24;
        
        self::assertEquals($this->network, $this->network->setCidr($actual));
        self::assertEquals($expected, $this->network->getCidr());
    }

    /**
     * Tests color getter, setter and aliases.
     */
    public function testColor()
    {
        $expected = $actual = 'color';
        
        self::assertEquals($this->network, $this->network->setColor($actual));
        self::assertEquals($expected, $this->network->getColor());
    }

    /**
     * Tests Network get add removeIp ().
     */
    public function testIps()
    {
        $ip1 = new Ip();
        $ip2 = new Ip();
        $this->network->addIp($ip1);
        self::assertCount(1, $this->network->getIps());
        self::assertTrue($this->network->getIps()->contains($ip1));
        self::assertFalse($this->network->getIps()->contains($ip2));
        $this->network->addIp($ip2);
        self::assertCount(2, $this->network->getIps());
        self::assertTrue($this->network->getIps()->contains($ip1));
        self::assertTrue($this->network->getIps()->contains($ip2));
        $this->network->removeIp($ip1);
        self::assertCount(1, $this->network->getIps());
        self::assertFalse($this->network->getIps()->contains($ip1));
        self::assertTrue($this->network->getIps()->contains($ip2));
        $this->network->removeIp($ip2);
        self::assertCount(0, $this->network->getIps());
        self::assertFalse($this->network->getIps()->contains($ip1));
        self::assertFalse($this->network->getIps()->contains($ip2));
    }
}
