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

namespace Controllers\Admin\Home;

class Data extends \Controllers\Base {
    use \Traits\DateRange;

    public function getChart(int $apiKey): array {
        $request = $this->f3->get('REQUEST');
        $type = $request['type'];
        $mode = $request['mode'];
        $modelMap = \Utils\Constants::CHART_MODEL_MAP;

        $model = array_key_exists($mode, $modelMap) ? new $modelMap[$mode]() : null;

        if (in_array($mode, \Utils\Constants::LINE_CHARTS)) {
            return $model->getData($apiKey);
        }

        $itemsByDate = [];
        $items = $model ? $model->getData($apiKey) : [];

        foreach ($items as $item) {
            $ts = strtotime($item['day']);
            $dt = date('Y-m-d', $ts);
            $itemsByDate[$dt] = $item['event_count'];
        }

        $datesRange = $this->getLatest180DatesRange();
        $itemsByDate = $this->addEmptyDays($itemsByDate, $datesRange);

        $ox = [];
        $data = [];

        foreach ($itemsByDate as $key => $value) {
            $ox[] = strtotime($key);
            $data[] = $value;
        }

        return [$ox, $data];
    }

    //TODO: move to base chart model component and use in the Bar charts models
    private function addEmptyDays(array $itemsByDate, array $datesRange): array {
        $endTs = strtotime($datesRange['endDate']);
        $startTs = strtotime($datesRange['startDate']);

        while ($endTs > $startTs) {
            $dt = date('Y-m-d', $startTs);
            if (!isset($itemsByDate[$dt])) {
                $itemsByDate[$dt] = null;
            }

            $startTs = strtotime('+1 day', $startTs);
        }

        ksort($itemsByDate);

        return $itemsByDate;
    }

    public function getStat(int $apiKey): array {
        $model = new \Models\Dashboard();

        $statByPeriod = $model->getStatWithDateRange($apiKey);
        $allTimeStat = $model->getStatWithoutDateRange($apiKey);

        return [
            'events' => $statByPeriod['events'],
            'eventsAllTime' => $allTimeStat['events'],

            'users' => $statByPeriod['users'],
            'usersAllTime' => $allTimeStat['users'],

            'ips' => $statByPeriod['ips'],
            'ipsAllTime' => $allTimeStat['ips'],

            'countries' => $statByPeriod['countries'],
            'countriesAllTime' => $allTimeStat['countries'],

            'resources' => $statByPeriod['resources'],
            'resourcesAllTime' => $allTimeStat['resources'],

            'blockedUsers' => $statByPeriod['blockedUsers'],
            'blockedUsersAllTime' => $allTimeStat['blockedUsers'],

            'usersForReview' => $statByPeriod['usersForReview'],
            'usersForReviewAllTime' => $allTimeStat['usersForReview'],
        ];
    }

    public function getTopTen(int $apiKey): array {
        $params = $this->f3->get('GET');
        $dateRange = $this->getDatesRange($params);
        $mode = $params['mode'];
        $modelMap = \Utils\Constants::TOP_TEN_MODELS_MAP;

        $model = array_key_exists($mode, $modelMap) ? new $modelMap[$mode]() : null;
        $data = $model ? $model->getList($apiKey, $dateRange) : [];
        $total = count($data);

        return [
            'draw' => $params['draw'] ?? 1,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ];
    }
}
