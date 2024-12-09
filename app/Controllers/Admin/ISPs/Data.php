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

namespace Controllers\Admin\ISPs;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $result = [];
        $model = new \Models\Grid\Isps\Grid($apiKey);

        $userId = $this->f3->get('REQUEST.userId');
        $domainId = $this->f3->get('REQUEST.domainId');
        $countryId = $this->f3->get('REQUEST.countryId');
        $resourceId = $this->f3->get('REQUEST.resourceId');

        if (isset($userId) && is_numeric($userId)) {
            $result = $model->getIspsByUserId($userId);
        }

        if (isset($domainId) && is_numeric($domainId)) {
            $result = $model->getIspsByDomainId($domainId);
        }

        if (isset($countryId) && is_numeric($countryId)) {
            $result = $model->getIspsByCountryId($countryId);
        }

        if (isset($resourceId) && is_numeric($resourceId)) {
            $result = $model->getIspsByResourceId($resourceId);
        }

        if (!$result) {
            $result = $model->getAllIsps();
        }

        $ids = array_column($result['data'], 'id');
        if ($ids) {
            $model = new \Models\Isp();
            $model->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = $model->refreshTotals($result['data'], $apiKey);
        }
        return $result;
    }
}
