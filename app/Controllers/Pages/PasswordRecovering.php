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

class PasswordRecovering extends Base {
    public $page = 'PasswordRecovering';

    public function getPageParams(): array {
        $pageParams = [
            'HTML_FILE' => 'passwordRecovering.html',
        ];

        $renewKey = $this->f3->get('PARAMS.renewKey');
        $errorCode = $this->validate($renewKey);
        $pageParams['SUCCESS_CODE'] = $errorCode;

        if ($this->isPostRequest()) {
            $params = $this->f3->get('POST');
            $errorCode = $this->validatePost($params);

            $pageParams['SUCCESS_CODE'] = 0;
            $pageParams['ERROR_CODE'] = $errorCode;

            if (!$errorCode) {
                $forgotPasswordModel = new \Models\ForgotPassword();
                $forgotPasswordModel->getUnusedByRenewKey($renewKey);
                $operatorId = $forgotPasswordModel->operator_id;

                $forgotPasswordModel->deactivate();

                $params = [
                    'id' => $operatorId,
                    'new-password' => $params['new-password'],
                ];

                $operatorModel = new \Models\Operator();
                $operatorModel->updatePassword($params);
                $operatorModel->activateByOperator($operatorId);

                $pageParams['SUCCESS_CODE'] = \Utils\ErrorCodes::ACCOUNT_ACTIVATED;
            }
        }

        return parent::applyPageParams($pageParams);
    }

    private function validate(string $renewKey): int|false {
        if (!$renewKey) {
            return \Utils\ErrorCodes::RENEW_KEY_DOES_NOT_EXIST;
        }

        $forgotPasswordModel = new \Models\ForgotPassword();
        $forgotPasswordModel->getUnusedByRenewKey($renewKey);
        if (!$forgotPasswordModel->loaded()) {
            return \Utils\ErrorCodes::RENEW_KEY_IS_NOT_CORRECT;
        }

        $currentTime = time();
        $linkTime = strtotime($forgotPasswordModel->created_at);
        $lifeTime = $this->f3->get('RENEW_PASSWORD_LINK_TIME');

        if ($currentTime > $linkTime + $lifeTime) {
            return \Utils\ErrorCodes::RENEW_KEY_WAS_EXPIRED;
        }

        return false;
    }

    private function validatePost(array $params): int|false {
        $errorCode = \Utils\Access::CSRFTokenValid($params, $this->f3);
        if ($errorCode) {
            return $errorCode;
        }

        $newPassword = $params['new-password'];
        if (!$newPassword) {
            return \Utils\ErrorCodes::NEW_PASSWORD_DOES_NOT_EXIST;
        }

        $newPasswordLegth = strlen($newPassword);
        $minPasswordLegth = $this->f3->get('MIN_PASSWORD_LENGTH');
        if ($newPasswordLegth < $minPasswordLegth) {
            return \Utils\ErrorCodes::PASSWORD_IS_TO_SHORT;
        }

        $passwordConfirmation = $params['password-confirmation'];
        if (!$passwordConfirmation) {
            return \Utils\ErrorCodes::PASSWORD_CONFIRMATION_DOES_NOT_EXIST;
        }

        if ($newPassword !== $passwordConfirmation) {
            return \Utils\ErrorCodes::PASSWORDS_ARE_NOT_EQUAL;
        }

        return false;
    }
}
