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

namespace Models\Search;

class Email extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'event_email';

    public function searchByEmail(string $query, int $apiKey): array {
        $params = [
            ':api_key' => $apiKey,
            ':query' => "%{$query}%",
        ];

        $query = (
            "SELECT
                event_email.account_id AS id,
                'Email'                 AS \"groupName\",
                'id'                    AS \"entityId\",
                event_email.email      AS value

            FROM
                event_email

            WHERE
                event_email.key = :api_key
                AND LOWER(event_email.email) LIKE LOWER(:query)

            LIMIT 25 OFFSET 0"
        );

        return $this->execQuery($query, $params);
    }
}
