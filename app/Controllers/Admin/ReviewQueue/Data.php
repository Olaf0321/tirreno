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

namespace Controllers\Admin\ReviewQueue;

class Data extends \Controllers\Base {
    public function getList(int $apiKey): array {
        $model = new \Models\Grid\ReviewQueue\Grid($apiKey);

        return $model->getAllUnderReviewUsers() ?? [];
    }

    public function getNumberOfNotReviewedUsers(int $apiKey, bool $useCache = true, bool $overall = false): array {
        $currentOperator = $this->f3->get('CURRENT_USER');
        $takeFromCache = $this->canTakeNumberOfNotReviewedUsersFromCache($currentOperator);

        $total = $currentOperator->review_queue_cnt;
        if (!$useCache || !$takeFromCache) {
            $model = new \Models\Grid\ReviewQueue\Grid($apiKey);
            $total = !$overall ? $model->getTotalUnderReviewUsers() : $model->getTotalUnderReviewUsersOverall();

            if ($total > 999) {
                $total = 999;
            }

            $data = [
                'id' => $currentOperator->id,
                'review_queue_cnt' => $total,
            ];

            $model = new \Models\Operator();
            $model->updateReviewedQueueCnt($data);
        }

        return ['total' => $total];
    }

    private function canTakeNumberOfNotReviewedUsersFromCache(\Models\Operator $currentOperator): bool {
        $diff = PHP_INT_MAX;
        $currentTime = gmdate('Y-m-d H:i:s');
        $updatedAt = $currentOperator->review_queue_updated_at;

        if ($updatedAt) {
            $dt1 = new \DateTime($currentTime);
            $dt2 = new \DateTime($updatedAt);

            $diff = $dt1->getTimestamp() - $dt2->getTimestamp();
        }

        $cacheTime = $this->f3->get('REVIEWED_QUEUE_CNT_CACHE_TIME');

        return $cacheTime > $diff;
    }
}
