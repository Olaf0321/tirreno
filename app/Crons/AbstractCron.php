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

namespace Crons;

abstract class AbstractCron {
    use \Traits\Db;
    use \Traits\Debug;

    protected $f3;

    public function __construct() {
        $this->f3 = \Base::instance();

        $this->connectToDb(false);
    }

    protected function log(string $message): void {
        $cronName = get_class($this);
        $cronName = substr($cronName, strrpos($cronName, '\\') + 1);
        echo sprintf('[%s] %s%s', $cronName, $message, PHP_EOL);
    }
}
