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

namespace Models;

class MapCountries extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'event';

    public function getCountriesByResource(int $apiKey, int $resourceId): array {
        $params = $this->getRequestParams($apiKey);
        $params[':resource_id'] = $resourceId;

        return $this->getCountries($params);
    }

    public function getCountriesByUser(int $apiKey, int $userId): array {
        $params = $this->getRequestParams($apiKey);
        // TODO: OK because getCountries() does SELECT with event_account.userid = :user_id
        $params[':user_id'] = $userId;

        return $this->getCountries($params);
    }

    public function getAllCountries($apiKey) {
        $params = $this->getRequestParams($apiKey);

        return $this->getCountries($params);
    }

    private function getCountries(array $params): array {
        $query = (
            'SELECT
                DISTINCT countries.id,
                countries.id AS country

            FROM
                event

            INNER JOIN event_account
            ON (event.account = event_account.id)

            INNER JOIN event_ip
            ON (event.ip = event_ip.id)

            INNER JOIN countries
            ON (event_ip.country = countries.serial)

            WHERE
                event.key = :api_key'
        );

        if (isset($params[':user_id'])) {
            $query .= ' AND event_account.userid = :user_id';
        }

        if (isset($params[':resource_id'])) {
            $query .= ' AND event.url = :resource_id';
        }

        return $this->execQuery($query, $params);
    }

    private function getRequestParams(int $apiKey): array {
        return [
            ':api_key' => $apiKey,
        ];
    }
}
