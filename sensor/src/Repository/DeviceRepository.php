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

namespace Sensor\Repository;

use Sensor\Entity\DeviceEntity;
use Sensor\Dto\InsertUserAgentDto;
use Sensor\Model\Validated\Timestamp;

class DeviceRepository {
    public function __construct(
        private UserAgentRepository $userAgentRepository,
        private \PDO $pdo,
    ) {
    }

    // check for device for account with current ua
    /** @return array{id: int}|false Returns the fetched data or false if no data found */
    private function existingId(int $uaId, ?string $lang, int $accountId, int $key): array|false {
        $sql = 'SELECT id FROM event_device WHERE key = :key AND account_id = :account_id AND user_agent = :ua_id FOR UPDATE LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':key', $key);
        $stmt->bindValue(':account_id', $accountId);
        $stmt->bindValue(':ua_id', $uaId);
        $stmt->execute();

        /** @var array{id: int}|false $result */
        $result = $stmt->fetch();

        return $result;
    }

    // find devices with same lang which user_agent is mostly the same
    /** @return array{id: int, user_agent: int, browser_version: string, os_version: string}|false */
    private function findSimilarDevice(DeviceEntity $device, InsertUserAgentDto $ua): array|false {
        $sql = 'SELECT
                event_device.id,
                event_device.user_agent,
                -- event_ua_parsed.browser_version,
                event_ua_parsed.os_version
            FROM
                event_device
            JOIN event_ua_parsed
            ON event_device.user_agent = event_ua_parsed.id
            WHERE
                event_device.account_id = :account_id AND
                event_device.key = :key AND
                event_ua_parsed.os_name = :os_name AND
                -- event_ua_parsed.browser_name = :browser_name AND
                event_ua_parsed.device = :device
            FOR UPDATE
            LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':account_id', $device->accountId);
        $stmt->bindValue(':key', $device->apiKeyId);
        $stmt->bindValue(':os_name', $ua->osName);
        //$stmt->bindValue(':browser_name', $ua->browserName);
        $stmt->bindValue(':device', $ua->device);
        $stmt->execute();

        /** @var array{id: int, user_agent: int, os_version: string}|false $result */
        $result = $stmt->fetch();

        return $result;
    }

    public function insert(DeviceEntity $device): int {
        $uaDto = $this->userAgentRepository->insertSwitch($device->userAgent);

        // Check if device with same ua, lang and account id exists
        $result = $this->existingId($uaDto->userAgentId, $device->lang, $device->accountId, $device->apiKeyId);

        // same device already exists
        if ($result !== false) {
            $sql = 'UPDATE event_device
                SET
                    lastseen = :lastseen,
                    lang = :lang
                WHERE
                    id = :id AND key = :key';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':lastseen', $device->lastSeen->format(Timestamp::EVENTFORMAT));
            $stmt->bindValue(':id', $result['id']);
            $stmt->bindValue(':key', $device->apiKeyId);
            $stmt->bindValue(':lang', $device->lang);
            $stmt->execute();

            return $result['id'];
        }

        // device is new or was overriden earlier in `event_devices`
        $result = $this->findSimilarDevice($device, $uaDto);

        if ($result !== false) {
            // select how to override (actualy choose newer useragent id)
            // $similarDevice = new InsertUserAgentDto($result['user_agent'], $result['os_name'], $result['os_version'], $result['browser_name'], $result['browser_version'], $result['device']);
            // $finalUserAgentId = $this->userAgentRepository->newerUserAgent($uaDto, $similarDevice);
            // for now always use new came user agent
            $finalUserAgentId = $uaDto->userAgentId;
            $sql = 'UPDATE event_device
                SET
                    user_agent = :user_agent, lastseen = :lastseen, lang = :lang
                WHERE
                    id = :id AND key = :key';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_agent', $finalUserAgentId);
            $stmt->bindValue(':lastseen', $device->lastSeen->format(Timestamp::EVENTFORMAT));
            $stmt->bindValue(':lang', $device->lang);
            $stmt->bindValue(':id', $result['id']);
            $stmt->bindValue(':key', $device->apiKeyId);
        } else {
            // no similar devices, create a new one. ON CONFLICT just update lastseen and lang in case of parallel transactions confusion
            $sql = 'INSERT INTO event_device
                    (account_id, key, user_agent, lang, lastseen, created, updated)
                VALUES
                    (:account_id, :key, :user_agent, :lang, :lastseen, :created, :updated)
                ON CONFLICT (key, account_id, user_agent) DO UPDATE
                SET
                    lang = EXCLUDED.lang, lastseen = EXCLUDED.lastseen
                RETURNING id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':account_id', $device->accountId);
            $stmt->bindValue(':key', $device->apiKeyId);
            $stmt->bindValue(':user_agent', $uaDto->userAgentId);
            $stmt->bindValue(':lang', $device->lang);
            $stmt->bindValue(':lastseen', $device->lastSeen->format(Timestamp::EVENTFORMAT));
            $stmt->bindValue(':created', $device->lastSeen->format(Timestamp::EVENTFORMAT));
            $stmt->bindValue(':updated', $device->lastSeen->format(Timestamp::EVENTFORMAT));
            $stmt->execute();

            /** @var array{id: int} $result */
            $result = $stmt->fetch();
        }

        return $result['id'];
    }

    private function findLatestIdByAccount(
        int $accountId,
        int $apiKeyId,
    ): ?int {
        $sql = 'SELECT id FROM event_device WHERE account_id = :account_id AND key = :key ORDER BY lastseen DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':account_id', $accountId);
        $stmt->bindValue(':key', $apiKeyId);
        $stmt->execute();

        /** @var array{id: int}|false $result */
        $result = $stmt->fetch();

        return $result === false ? null : $result['id'];
    }

    private function findLatestIdForOs(
        int $accountId,
        int $apiKeyId,
        string $osName,
        string $device,
    ): ?int {
        $sql = 'SELECT event_device.id
            FROM
                event_device
            JOIN event_ua_parsed ON event_device.user_agent = event_ua_parsed.id
            WHERE
                event_device.account_id = :account_id AND
                event_device.key = :key AND
                event_ua_parsed.os_name = :os_name AND
                event_ua_parsed.device = :device
            ORDER BY lastseen DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':account_id', $accountId);
        $stmt->bindValue(':key', $apiKeyId);
        $stmt->bindValue(':os_name', $osName);
        $stmt->bindValue(':device', $device);
        $stmt->execute();

        /** @var array{id: int}|false $result */
        $result = $stmt->fetch();

        return $result === false ? null : $result['id'];
    }
}
