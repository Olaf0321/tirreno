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

namespace Utils;

class Logger {
    public static function log(?string $title, string|array $message): void {
        $f3 = \Base::instance();
        $logFile = $f3->get('LOG_FILE');
        $logger = new \Log($logFile);

        if (is_array($message)) {
            $message = var_export($message, true);
        }

        if ($title) {
            $message = sprintf('%s:%s%s', $title, PHP_EOL, $message);
        }

        $logger->write($message);
    }

    public static function logSql(string $title, string $message): void {
        $f3 = \Base::instance();
        $logFile = $f3->get('LOG_SQL_FILE');
        $logDelimiter = $f3->get('LOG_DELIMITER');

        $logger = new \Log($logFile);
        $logger->write($title . ':' . PHP_EOL . $message . $logDelimiter);
    }
}
