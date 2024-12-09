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

namespace Controllers\Admin\Domains;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $result = [];
        $model = new \Models\Grid\Domains\Grid($apiKey);

        $domainId = $this->f3->get('REQUEST.domainId');

        if (isset($domainId)) {
            $result = $model->getDomainsBySameIpDomainId($domainId);
        }

        if (!$result) {
            $result = $model->getAllDomains();
        }

        $ids = array_column($result['data'], 'id');
        if ($ids) {
            $model = new \Models\Domain();
            $model->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = $model->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }
}
