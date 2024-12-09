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

namespace Traits;

trait Debug {
    public function e($value, bool $shouldExit = false): void {
        \Utils\HotDebug::e($value, $shouldExit);
    }

    public function l(?string $title, string $message): void {
        $title = $title ?? 'Custom Log Message';
        \Utils\Logger::log($title, $message);
    }
}
