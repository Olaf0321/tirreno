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

namespace Controllers\Admin\Users;

class Data extends \Controllers\Base {
    use \Traits\DateRange;

    public function getList(int $apiKey): array {
        $result = [];
        $model = new \Models\Grid\Users\Grid($apiKey);

        $ipId = $this->f3->get('REQUEST.ipId');
        $ispId = $this->f3->get('REQUEST.ispId');
        $botId = $this->f3->get('REQUEST.botId');
        $domainId = $this->f3->get('REQUEST.domainId');
        $countryId = $this->f3->get('REQUEST.countryId');
        $resourceId = $this->f3->get('REQUEST.resourceId');

        if (isset($ipId) && is_numeric($ipId)) {
            $result = $model->getUsersByIpId($ipId, $apiKey);
        }

        if (isset($ispId)) {
            $result = $model->getUsersByIspId($ispId, $apiKey);
        }

        if (isset($domainId) && is_numeric($domainId)) {
            $result = $model->getUsersByDomainId($domainId, $apiKey);
        }

        if (isset($countryId) && is_numeric($countryId)) {
            $result = $model->getUsersByCountryId($countryId, $apiKey);
        }

        if (isset($botId) && is_numeric($botId)) {
            $result = $model->getUsersByDeviceId($botId, $apiKey);
        }

        if (isset($resourceId) && is_numeric($resourceId)) {
            $result = $model->getUsersByResourceId($resourceId, $apiKey);
        }

        if (!$result) {
            $result = $model->getAllUsers();
        }

        $ids = array_column($result['data'], 'id');
        if ($ids) {
            $model = new \Models\User();
            $model->updateTotalsByAccountIds($ids, $apiKey);
            $result['data'] = $model->refreshTotals($result['data'], $apiKey);
        }

        return $result;
    }
}
