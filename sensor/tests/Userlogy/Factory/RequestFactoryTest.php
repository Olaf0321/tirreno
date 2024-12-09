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

namespace Sensor\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sensor\Exception\ValidationException;
use Sensor\Model\Validated\Email;

class RequestFactoryTest extends TestCase {
    #[DataProvider('usernameEmailProvider')]
    public function testCreateFromArrayUsernameEmail(
        string $username,
        string $email,
        string $expectedUsername,
        ?string $expectedEmail,
    ): void {
        $data = [
            'ipAddress' => '127.0.0.1',
            'url' => '/',
            'eventTime' => 'now',
            'userAgent' => 'Mozilla/5.0',
            'emailAddress' => $email,
            'userName' => $username,
        ];

        $factory = new RequestFactory();
        $dto = $factory->createFromArray($data, null);

        $this->assertEquals($expectedUsername, $dto->userName);
        $this->assertEquals($expectedEmail, $dto->emailAddress?->value);
    }

    /**
     * @return array<string, mixed>
     */
    public static function usernameEmailProvider(): array {
        return [
            'Full data' => [
                'username' => '1',
                'email' => 'test@test.com',
                'expectedUsername' => '1',
                'expectedEmail' => 'test@test.com',
            ],
            'No email' => [
                'username' => '1',
                'email' => '',
                'expectedUsername' => '1',
                'expectedEmail' => null,
            ],
            'No username' => [
                'username' => '',
                'email' => 'test@test.com',
                'expectedUsername' => md5('test@test.com'),
                'expectedEmail' => 'test@test.com',
            ],
            'No data' => [
                'username' => '',
                'email' => '',
                'expectedUsername' => md5(Email::makePlaceholder()->value),
                'expectedEmail' => Email::makePlaceholder()->value,
            ],
        ];
    }

    public function testCreateFromArrayInvalidEmail(): void {
        $data = [
            'ipAddress' => '127.0.0.1',
            'url' => '/',
            'eventTime' => 'now',
            'userAgent' => 'Mozilla/5.0',
            'emailAddress' => 'invalid',
            'userName' => '1',
        ];

        $factory = new RequestFactory();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Email is invalid');
        $dto = $factory->createFromArray($data, null);
    }
}
