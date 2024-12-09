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

class BlacklistItems extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'event';

    public function getIpsRelatedToAccountWithinOperator(int $accountId, int $apiKey): array {
        $params = [
            ':account_id' => $accountId,
            ':api_key' => $apiKey,
        ];

        $query = ("
            SELECT
                event_ip.id,
                event_ip.ip AS value,
                'ip' AS type,
                :account_id::bigint AS account_id
            FROM event_ip
            WHERE
                (id, key) IN (
                    SELECT ip, key
                    FROM event
                    WHERE
                        account = :account_id
                        AND key = :api_key)");

        return $this->execQuery($query, $params);
    }

    public function getEmailsRelatedToAccountWithinOperator(int $accountId, int $apiKey): array {
        $params = [
            ':account_id' => $accountId,
            ':api_key' => $apiKey,
        ];

        $query = ("
            SELECT
                id,
                email AS value,
                'email' AS type,
                account_id
            FROM
                event_email
            WHERE
                key = :api_key
                AND account_id = :account_id
        ");

        return $this->execQuery($query, $params);
    }

    public function getPhonesRelatedToAccountWithinOperator(int $accountId, int $apiKey): array {
        $params = [
            ':account_id' => $accountId,
            ':api_key' => $apiKey,
        ];

        $query = ("
            SELECT
                id,
                phone_number AS value,
                'phone' AS type,
                account_id
            FROM
                event_phone
            WHERE
                key = :api_key
                AND account_id = :account_id
        ");

        return $this->execQuery($query, $params);
    }

    public function searchBlacklistedItem(int $apiKey, string $type, string $value): ?bool {
        $query = '';
        $params = [
            ':value' => $value,
            ':api_key' => $apiKey,
        ];

        switch ($type) {
            case 'account':
                $query = ('
                    SELECT 1
                    FROM event_account
                    WHERE
                        userid = :value AND
                        fraud IS TRUE AND
                        key = :api_key
                    LIMIT 1');
                break;

            case 'ip':
                if (filter_var($value, FILTER_VALIDATE_IP)) {
                    $query = ('
                        SELECT 1
                        FROM event_ip
                        WHERE
                            ip = :value AND
                            fraud_detected IS TRUE AND
                            key = :api_key
                        LIMIT 1');
                }
                break;

            case 'email':
                $query = ('
                    SELECT 1
                    FROM event_email
                    WHERE
                        email = :value AND
                        fraud_detected IS TRUE AND
                        key = :api_key
                    LIMIT 1');
                break;

            case 'phone':
                $query = ('
                    SELECT 1
                    FROM event_phone
                    WHERE
                        phone_number = :value AND
                        fraud_detected IS TRUE AND
                        key = :api_key
                    LIMIT 1');
                break;
        }

        if ($query) {
            $results = $this->execQuery($query, $params);

            return (bool) count($results);
        }

        return null;
    }
}
