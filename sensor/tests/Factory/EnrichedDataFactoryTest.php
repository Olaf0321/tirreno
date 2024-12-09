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

namespace Tests\Unit\Sensor\Factory;

use PHPUnit\Framework\TestCase;
use Sensor\Factory\EnrichedDataFactory;
use Sensor\Model\Enriched\DomainNotFoundEnriched;
use Sensor\Service\Logger;

class EnrichedDataFactoryTest extends TestCase {
    public function testInvalidPhoneResponse(): void {
        $expectedLogMessage = 'Error getting phone from Enrichment API: {"phone_number":"1-800","invalid":true,"validation_error":"INVALID_COUNTRY_CODE"}';

        $logger = $this->createMock(Logger::class);
        $logger
            ->expects($this->once())
            ->method('logWarning')
            ->with($expectedLogMessage)
        ;

        $factory = new EnrichedDataFactory($logger);
        $data = ['phone_number' => '1-800', 'invalid' => true, 'validation_error' => 'INVALID_COUNTRY_CODE'];
        $result = $factory->createFromResponse(['phone' => $data]);
        $this->assertNull($result->phone);
        $this->assertEquals('INVALID_COUNTRY_CODE', $result->phoneValidationError);
    }

    public function testInvalidDomainResponse(): void {
        $logger = $this->createMock(Logger::class);
        $factory = new EnrichedDataFactory($logger);
        $data = [
            'domain' => 'totalydoesntexistdomain.com',
            'blockdomains' => false,
            'disposable_domains' => false,
            'free_email_provider' => false,
            'creation_date' => null,
            'expiration_date' => null,
            'return_code' => null,
            'disabled' => true,
            'closest_snapshot' => null,
            'mx_record' => false,
        ];
        $result = $factory->createFromResponse(['domain' => $data]);
        $this->assertInstanceOf(DomainNotFoundEnriched::class, $result->domain);
    }

    public function testIpIsBogon(): void {
        $logger = $this->createMock(Logger::class);
        $factory = new EnrichedDataFactory($logger);
        $data = [
            'value' => '127.0.0.1',
            'type' => 'ip',
            'error' => 'IP is bogon',
        ];

        $result = $factory->createFromResponse(['ip' => $data]);
        $this->assertNull($result->ip);
        $this->assertTrue($result->ipIsBogon);
    }

    public function testIpIsBogonFalse(): void {
        $logger = $this->createMock(Logger::class);
        $factory = new EnrichedDataFactory($logger);
        $data = [
            'value' => '127.0.0.1',
            'type' => 'ip',
            'error' => 'Some error',
        ];

        $result = $factory->createFromResponse(['ip' => $data]);
        $this->assertNull($result->ip);
        $this->assertFalse($result->ipIsBogon);
    }
}
