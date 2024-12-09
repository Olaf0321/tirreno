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

namespace Models\UserDetails;

class Domain extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'event_account';

    public function getDetails(int $userId, int $apiKey): array {
        $params = [
            ':user_id' => $userId,
            ':api_key' => $apiKey,
        ];

        $query = (
            'SELECT
                event_domain.domain,
                event_domain.disabled,
                event_domain.total_account,
                event_domain.disposable_domains,
                event_domain.blockdomains,
                event_domain.tranco_rank,
                event_domain.creation_date,
                event_domain.expiration_date

            FROM
                event_account

            LEFT JOIN event_email
            ON event_account.lastemail = event_email.id

            LEFT JOIN event_domain
            ON event_email.domain = event_domain.id

            WHERE
                event_account.key = :api_key
                AND event_account.id = :user_id'
        );

        $results = $this->execQuery($query, $params);

        return $results[0] ?? [];
    }
}
