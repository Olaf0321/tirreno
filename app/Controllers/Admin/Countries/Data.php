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

namespace Controllers\Admin\Countries;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $result = [];
        $model = new \Models\Grid\Countries\Grid($apiKey);

        $ispId = $this->f3->get('REQUEST.ispId');
        $userId = $this->f3->get('REQUEST.userId');
        $botId = $this->f3->get('REQUEST.botId');
        $domainId = $this->f3->get('REQUEST.domainId');
        $resourceId = $this->f3->get('REQUEST.resourceId');

        if (isset($userId) && is_numeric($userId)) {
            $result = $model->getCountriesByUserId($userId);
        }

        if (isset($ispId)) {
            $result = $model->getCountriesByIspId($ispId);
        }

        if (isset($domainId) && is_numeric($domainId)) {
            $result = $model->getCountriesByDomainId($domainId);
        }

        if (isset($botId) && is_numeric($botId)) {
            $result = $model->getCountriesByDeviceId($botId);
        }

        if (isset($resourceId) && is_numeric($resourceId)) {
            $result = $model->getCountriesByResourceId($resourceId);
        }

        if (!$result) {
            $result = $model->getAllCountries();
            // refresh totals only for grid
            if ($this->f3->get('REQUEST.draw')) {
                $ids = array_column($result['data'], 'id');
                if ($ids) {
                    $model = new \Models\Country();
                    $model->updateTotalsByEntityIds($ids, $apiKey);
                    $result['data'] = $model->refreshTotals($result['data'], $apiKey);
                }
            }
        }

        return $result;
    }
}
