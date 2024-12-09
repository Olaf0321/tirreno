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

namespace Models;

class NotificationPreferences extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'dshb_operators';

    /**
     * @param string[] $timezones
     */
    public function listOperatorsEligableForUnreviewedItemsReminder(array $timezones): array {
        [$params, $placeHolders] = $this->getArrayPlaceholders($timezones);
        $params = \array_merge($params, [
            ':daily' => \Type\UnreviewedItemsReminderFrequencyType::Daily,
            ':weekly' => \Type\UnreviewedItemsReminderFrequencyType::Weekly,
            ':off' => \Type\UnreviewedItemsReminderFrequencyType::Off,
        ]);

        $query = \sprintf('SELECT *
            FROM dshb_operators
            WHERE
                timezone IN (%s)
                AND unreviewed_items_reminder_freq != :off
                AND review_queue_cnt > 0
                AND (last_unreviewed_items_reminder IS NULL
                    OR (unreviewed_items_reminder_freq = :daily AND last_unreviewed_items_reminder <= NOW() - \'1 day\'::interval)
                    OR (unreviewed_items_reminder_freq = :weekly AND last_unreviewed_items_reminder <= NOW() - \'7 day\'::interval)
                );
        ', $placeHolders);

        return $this->execQuery($query, $params);
    }

    /**
     * @param int[] $operatorsIds
     */
    public function updateLastUnreviewedItemsReminder(array $operatorsIds): void {
        [$params, $placeHolders] = $this->getArrayPlaceholders($operatorsIds);

        $query = \sprintf('UPDATE dshb_operators
            SET last_unreviewed_items_reminder = NOW()
            WHERE "id" IN (%s);
        ', $placeHolders);

        $this->execQuery($query, $params);
    }
}
