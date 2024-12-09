<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

declare(strict_types=1);

namespace Tests\Unit\Sensor\Model\Validated;

use PHPUnit\Framework\TestCase;
use Sensor\Model\Validated\IpAddress;

class IpAddressTest extends TestCase {
    public function testConstructWithValidIPv4Address(): void {
        $ipAddress = new IpAddress('192.168.1.1');
        $this->assertEquals('192.168.1.1', $ipAddress->value);
    }

    public function testConstructWithValidIPv6Address(): void {
        $ipAddress = new IpAddress('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
        $this->assertEquals('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $ipAddress->value);
    }

    public function testConstructWithValidIPv4AddressAndSpaces(): void {
        $ipAddress = new IpAddress(' 192.168.1.1 ');
        $this->assertEquals('192.168.1.1', $ipAddress->value);
    }

    public function testConstructWithInvalidIPAddress(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid IP address');
        new IpAddress('invalid_ip');
    }
}
