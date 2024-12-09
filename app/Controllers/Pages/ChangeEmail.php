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

namespace Controllers\Pages;

class ChangeEmail extends Base {
    public $page = 'ChangeEmail';

    public function getPageParams(): array {
        $pageParams = [
            'HTML_FILE' => 'changeEmail.html',
        ];

        $renewKey = $this->f3->get('PARAMS.renewKey');
        $errorCode = $this->validate($renewKey);
        $pageParams['SUCCESS_CODE'] = $errorCode;

        if (!$errorCode) {
            //logout
            $this->f3->clear('SESSION');
            session_commit();

            //change email
            $changeEmailModel = new \Models\ChangeEmail();
            $changeEmailModel->getByRenewKey($renewKey);

            $newEmail = $changeEmailModel->email;
            $operatorId = $changeEmailModel->operator_id;

            $changeEmailModel->deactivate();

            $params = [
                'id' => $operatorId,
                'email' => $newEmail,
            ];
            $operatorModel = new \Models\Operator();
            $operatorModel->updateEmail($params);

            //update success message
            $pageParams['SUCCESS_CODE'] = \Utils\ErrorCodes::EMAIL_CHANGED;
        }

        return parent::applyPageParams($pageParams);
    }

    private function validate($renewKey): int|false {
        if (!$renewKey) {
            return \Utils\ErrorCodes::CHANGE_EMAIL_KEY_DOES_NOT_EXIST;
        }

        $changeEmailModel = new \Models\ChangeEmail();
        $changeEmailModel->getByRenewKey($renewKey);
        if (!$changeEmailModel->loaded()) {
            return \Utils\ErrorCodes::CHANGE_EMAIL_KEY_IS_NOT_CORRECT;
        }

        $currentTime = time();
        $linkTime = strtotime($changeEmailModel->created_at);
        $lifeTime = $this->f3->get('RENEW_PASSWORD_LINK_TIME');

        if ($currentTime > $linkTime + $lifeTime) {
            return \Utils\ErrorCodes::CHANGE_EMAIL_KEY_WAS_EXPIRED;
        }

        return false;
    }
}
