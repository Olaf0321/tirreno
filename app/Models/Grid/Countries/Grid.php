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

namespace Models\Grid\Countries;

class Grid extends \Models\Grid\Base\Grid {
    public function __construct(int $apiKey) {
        parent::__construct();

        $this->apiKey = $apiKey;
        $this->idsModel = new Ids($apiKey);
        $this->query = new Query($apiKey);
    }

    public function getCountriesByUserId(int $userId): array {
        $params = ['userId' => $userId];

        return $this->getGrid($this->idsModel->getCountriesIdsByUserId(), $params);
    }

    public function getCountriesByIspId(int $ispId): array {
        $params = ['ispId' => $ispId];

        return $this->getGrid($this->idsModel->getCountriesIdsByIspId(), $params);
    }

    public function getCountriesByDomainId(int $domainId): array {
        $params = ['domainId' => $domainId];

        return $this->getGrid($this->idsModel->getCountriesIdsByDomainId(), $params);
    }

    public function getCountriesByDeviceId(int $deviceId): array {
        $params = ['deviceId' => $deviceId];

        return $this->getGrid($this->idsModel->getCountriesIdsByDeviceId(), $params);
    }

    public function getCountriesByResourceId(int $resourceId): array {
        $params = ['resourceId' => $resourceId];

        return $this->getGrid($this->idsModel->getCountriesIdsByResourceId(), $params);
    }

    public function getAllCountries(): array {
        return $this->getGrid();
    }
}
