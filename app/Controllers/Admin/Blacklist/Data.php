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

namespace Controllers\Admin\Blacklist;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $model = new \Models\Grid\Blacklist\Grid($apiKey);

        return $model->getAllBlacklistedItems();
    }

    public function removeItemFromBlacklist(int $itemId, string $type, int $apiKey): void {
        $model = null;

        if ($type === 'ip') {
            $model = new \Models\Ip();
        }
        if ($type === 'email') {
            $model = new \Models\Email();
        }
        if ($type === 'phone') {
            $model = new \Models\Phone();
        }

        $model->updateFraudFlag([$itemId], false, $apiKey);
    }
}
