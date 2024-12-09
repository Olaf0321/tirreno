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

namespace Controllers\Admin\IPs;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $result = [];
        $model = new \Models\Grid\Ips\Grid($apiKey);

        $ispId = $this->f3->get('REQUEST.ispId');
        $userId = $this->f3->get('REQUEST.userId');
        $botId = $this->f3->get('REQUEST.botId');
        $domainId = $this->f3->get('REQUEST.domainId');
        $countryId = $this->f3->get('REQUEST.countryId');
        $resourceId = $this->f3->get('REQUEST.resourceId');

        if (isset($userId) && is_numeric($userId)) {
            $result = $model->getIpsByUserId($userId);
        }

        if (isset($ispId)) {
            $result = $model->getIpsByIspId($ispId);
        }

        if (isset($domainId) && is_numeric($domainId)) {
            $result = $model->getIpsByDomainId($domainId);
        }

        if (isset($countryId) && is_numeric($countryId)) {
            $result = $model->getIpsByCountryId($countryId);
        }

        if (isset($botId) && is_numeric($botId)) {
            $result = $model->getIpsByDeviceId($botId);
        }

        if (isset($resourceId) && is_numeric($resourceId)) {
            $result = $model->getIpsByResourceId($resourceId);
        }

        if (!$result) {
            $result = $model->getAllIps();
        }

        $ids = array_column($result['data'], 'id');
        if ($ids) {
            $model = new \Models\Ip();
            $model->updateTotalsByEntityIds($ids, $apiKey);
            $result['data'] = $model->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }
}
