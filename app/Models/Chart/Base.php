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

namespace Models\Chart;

abstract class Base extends \Models\BaseSql {
    use \Traits\DateRange;

    private function getDt(array $item): string {
        $ts = strtotime($item['day']);

        return date('Y-m-d', $ts);
    }

    protected function concatDataLines(array $data1, string $field1, array $data2, string $field2, array $data3 = [], ?string $field3 = null): array {
        $data0 = [];

        for ($i = 0; $i < count($data1); ++$i) {
            $item = $data1[$i];

            $dt = $this->getDt($item);
            $data0[$dt] = [];

            $data0[$dt]['day'] = $dt;
            $data0[$dt][$field1] = $item[$field1];
            $data0[$dt][$field2] = 0;

            if ($field3) {
                $data0[$dt][$field3] = 0;
            }
        }

        for ($i = 0; $i < count($data2); ++$i) {
            $item = $data2[$i];

            $dt = $this->getDt($item);
            $isExist = $data0[$dt] ?? null;

            if (!$isExist) {
                $data0[$dt] = [];

                $data0[$dt]['day'] = $dt;
                $data0[$dt][$field1] = 0;
                $data0[$dt][$field2] = 0;
            }

            $data0[$dt][$field2] = $item[$field2];
        }

        for ($i = 0; $i < count($data3); ++$i) {
            $item = $data3[$i];

            $dt = $this->getDt($item);
            $isExist = $data0[$dt] ?? null;

            if (!$isExist) {
                $data0[$dt] = [];

                $data0[$dt]['day'] = $dt;
                $data0[$dt][$field1] = 0;
                $data0[$dt][$field2] = 0;
                $data0[$dt][$field3] = 0;
            }

            $data0[$dt][$field3] = $item[$field3];
        }

        // TODO: tmp order troubles fix
        usort($data0, function ($a, $b) {
            return strtotime($a['day']) - strtotime($b['day']);
        });

        return $data0;
    }

    protected function addEmptyDays(array $params): array {
        $n = count($params);
        $data = array_fill(0, $n, []);

        $request = $this->f3->get('REQUEST');
        $dateRange = $this->getDatesRange($request);

        if (!$dateRange) {
            for ($i = 0; $i < count($params[0]); ++$i) {
                $dt = $params[0][$i];
                $params[0][$i] = $this->formatDay($dt);
            }

            return $params;
        }

        $endDate = $dateRange['endDate'];
        $startDate = $dateRange['startDate'];

        $endTs = strtotime($endDate);
        $startTs = strtotime($startDate);

        $ox = $params[0];

        while ($endTs > $startTs) {
            $dt = date('Y-m-d', $startTs);

            $itemIndex = array_search($dt, $ox);
            $isExists = $itemIndex !== false;

            $data[0][] = $this->formatDay($dt);

            for ($i = 1; $i < $n; ++$i) {
                if ($isExists) {
                    $data[$i][] = $params[$i][$itemIndex];
                } else {
                    $data[$i][] = 0;
                }
            }

            $startTs = strtotime('+1 day', $startTs);
        }

        return $data;
    }

    protected function formatDay(string $dt) {
        $ts = strtotime($dt);
        $dt = date('Y-m-d', $ts);

        return strtotime($dt);
    }

    protected function execute(string $query, int $apiKey): array {
        $request = $this->f3->get('REQUEST');
        $dateRange = $this->getDatesRange($request);

        //Search request does not contain daterange param
        if (!$dateRange) {
            $dateRange = [
                'endDate' => date('Y-m-d H:i:s'),
                'startDate' => '1970-01-01',
            ];
        }

        $params = [
            ':api_key' => $apiKey,
            ':end_time' => $dateRange['endDate'],
            ':start_time' => $dateRange['startDate'],
        ];

        return $this->execQuery($query, $params);
    }
}
