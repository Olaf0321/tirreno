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

namespace Models\Grid\Countries;

class Ids extends \Models\Grid\Base\Ids {
    public function getCountriesIdsByUserId(): string {
        return (
            'SELECT DISTINCT
                event_ip.country AS itemid
            FROM event_ip
            INNER JOIN event
            ON (event_ip.id = event.ip)
            WHERE
                event_ip.key = :api_key AND
                event.account = :item_id'
        );
    }

    public function getCountriesIdsByIspId(): string {
        return (
            'SELECT DISTINCT
                event_ip.country AS itemid
            FROM event_ip
            WHERE
                event_ip.key = :api_key AND
                event_ip.isp = :item_id'
        );
    }

    public function getCountriesIdsByDomainId(): string {
        return (
            'SELECT DISTINCT
                event_ip.country AS itemid
            FROM event
            INNER JOIN event_ip
            ON (event.ip = event_ip.id)
            LEFT JOIN event_email
            ON event.email = event_email.id
            WHERE
                event_email.key = :api_key AND
                event_email.domain = :item_id'
        );
    }

    public function getCountriesIdsByDeviceId(): string {
        return (
            'SELECT DISTINCT
                event_ip.country AS itemid
            FROM event
            INNER JOIN event_ip
            ON (event.ip = event_ip.id)
            INNER JOIN event_device
            ON (event.device = event_device.id)
            INNER JOIN event_ua_parsed
            ON (event_device.user_agent = event_ua_parsed.id)
            WHERE
                event_ua_parsed.id = :item_id AND
                event_ua_parsed.key = :api_key'
        );
    }

    public function getCountriesIdsByResourceId(): string {
        return (
            'SELECT DISTINCT
                event_ip.country AS itemid
            FROM event
            INNER JOIN event_ip
            ON (event.ip = event_ip.id)
            WHERE
                event.url = :item_id AND
                event.key = :api_key'
        );
    }
}
