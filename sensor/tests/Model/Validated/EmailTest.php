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
use Sensor\Model\Validated\Email;

class EmailTest extends TestCase {
    public function testConstructWithValidEmail(): void {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->value);
    }

    public function testConstructWithValidEmailAndSpaces(): void {
        $email = new Email(' test@example.com ');
        $this->assertEquals('test@example.com', $email->value);
    }

    public function testConstructWithInvalidEmail(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Email is invalid');
        new Email('invalid_email');
    }

    public function testConstructWithInvalidEmailFormat(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Email is invalid');
        new Email('test@invalid.');
    }

    public function testPlaceholder(): void {
        $this->assertFalse(Email::isPlaceholder('test@example.com'));
        $placeholder = Email::makePlaceholder();
        $this->assertInstanceOf(Email::class, $placeholder);
        $this->assertTrue(Email::isPlaceholder($placeholder->value));
    }
}
