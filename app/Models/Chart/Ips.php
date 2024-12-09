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

namespace Models\Chart;

class Ips extends Base {
    protected $DB_TABLE_NAME = 'event';

    public function getData(int $apiKey): array {
        $data = $this->getFirstLine($apiKey);

        $ox = array_column($data, 'day');
        $l1 = array_column($data, 'residence_ip_count');
        $l2 = array_column($data, 'total_privacy');
        $l3 = array_column($data, 'suspicious_ip_count');

        return $this->addEmptyDays([$ox, $l1, $l2, $l3]);
    }

    private function getFirstLine(int $apiKey): array {
        $query = (
            "SELECT
                TEXT(date_trunc('day', event.time)::date) AS day,
                COUNT(DISTINCT event.ip) AS unique_ip_count,

                COUNT(DISTINCT
                    CASE
                        WHEN event_ip.data_center = 'True' OR
                             event_ip.tor = 'True' OR
                             event_ip.vpn = 'True'
                        THEN event.ip
                        ELSE NULL
                     END
                ) AS total_privacy,

                COUNT(DISTINCT event.ip) - COUNT(DISTINCT
                    CASE
                        WHEN event_ip.data_center = 'True' OR
                             event_ip.tor = 'True' OR
                             event_ip.vpn = 'True'
                        THEN event.ip
                        ELSE NULL
                    END
                ) AS residence_ip_count,

                COUNT(DISTINCT
                    CASE
                        WHEN event_ip.blocklist IS TRUE OR
                             event_ip.fraud_detected IS TRUE
                        THEN event.ip
                        ELSE NULL
                    END
                ) AS suspicious_ip_count

            FROM
                event

            INNER JOIN event_ip
            ON (event.ip = event_ip.id)

            WHERE
                event.key = :api_key
                AND event.time >= :start_time
                AND event.time <= :end_time

            GROUP BY
                day

            ORDER BY
                day"
        );

        return $this->execute($query, $apiKey);
    }
}
