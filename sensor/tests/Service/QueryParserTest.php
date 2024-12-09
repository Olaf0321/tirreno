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

namespace Tests\Unit\Sensor\Service;

use PHPUnit\Framework\TestCase;
use Sensor\Service\QueryParser;

final class QueryParserTest extends TestCase {
    public function testGetQueryFromUrl(): void {
        $queryParser = new QueryParser();

        $this->assertEquals('?x=1&y=2', $queryParser->getQueryFromUrl('/test?x=1&y=2'));
        $this->assertEquals('?a=b', $queryParser->getQueryFromUrl('/test?a=b'));
        $this->assertNull($queryParser->getQueryFromUrl('/test'));
    }
}
