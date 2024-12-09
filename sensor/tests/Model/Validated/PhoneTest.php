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
use Sensor\Model\Validated\Phone;

class PhoneTest extends TestCase {
    public function testConstructWithValidPhone(): void {
        $phone = new Phone('12345678901');
        $this->assertEquals('12345678901', $phone->value);
    }

    public function testConstructWithEmptyNumber(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Phone number is empty');
        new Phone('');
    }

    public function testConstructWithSpaceString(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Phone number is empty');
        new Phone('   ');
    }

    public function testConstructWithValidPhoneAndSpaces(): void {
        $phone = new Phone(' 123 456 789 01 ');
        $this->assertEquals('12345678901', $phone->value);
    }

    public function testConstructWithPhoneExceedingMaxLength(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid phone');
        new Phone('1234567890 or 122343243');
    }
}
