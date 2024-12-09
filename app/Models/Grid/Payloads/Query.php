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

namespace Models\Grid\Payloads;

class Query extends \Models\Grid\Base\Query {
    protected $defaultOrder = 'id DESC';
    protected $dateRangeField = 'event.time';

    public function getData(): array {
        $queryParams = $this->getQueryParams();

        $query = (
            'SELECT
                id, payload, time

            FROM
                event

            WHERE
                event.key = :api_key
                %s'
        );

        $this->applySearch($query, $queryParams);
        $this->applyOrder($query);
        $this->applyLimit($query, $queryParams);

        return [$query, $queryParams];
    }

    public function getTotal(): array {
        $queryParams = $this->getQueryParams();

        $query = (
            'SELECT
                COUNT (DISTINCT event.id)

            FROM
                event

            WHERE
                event.key = :api_key
                %s'
        );

        $this->applySearch($query, $queryParams);

        return [$query, $queryParams];
    }

    private function applySearch(string &$query, array &$queryParams): void {
        $userId = $this->f3->get('REQUEST.userId') ?? null;

        $searchConditions = '';
        if ($userId) {
            $searchConditions = (
                'AND payload IS NOT NULL
                AND event.account = :user_id'
            );
            $queryParams[':user_id'] = $userId;
        }

        $query = sprintf($query, $searchConditions);
    }
}
