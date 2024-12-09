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

class Email extends \Models\BaseSql {
    use \Traits\Enrichment\Emails;

    protected $DB_TABLE_NAME = 'event_account';

    public function getDetails(int $userId, int $apiKey): array {
        $params = [
            ':user_id' => $userId,
            ':api_key' => $apiKey,
        ];

        $query = (
            'SELECT
                event_email.email,
                event_email.data_breach,
                event_email.data_breaches,
                event_email.blockemails,
                event_email.profiles,
                event_email.fraud_detected,
                event_email.earliest_breach,

                event_domain.free_email_provider,
                event_domain.disposable_domains

            FROM
                event_account

            LEFT JOIN event_email
            ON event_account.lastemail = event_email.id

            LEFT JOIN event_domain
            ON (event_email.domain = event_domain.id)

            WHERE
                event_account.key = :api_key
                AND event_account.id = :user_id'
        );

        $results = $this->execQuery($query, $params);

        $this->calculateEmailReputation($results);

        return $results[0] ?? [];
    }
}
