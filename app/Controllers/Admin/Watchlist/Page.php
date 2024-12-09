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

namespace Controllers\Admin\Watchlist;

class Page extends \Controllers\Pages\Base {
    use \Traits\ApiKeys;

    public $page = 'AdminWatchlist';

    public function getPageParams(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        $dataController = new Data();
        $users = $dataController->getImportantUsers($apiKey);

        $searchPlacholder = $this->f3->get('AdminUsers_search_placeholder');

        $pageParams = [
            'SEARCH_PLACEHOLDER' => $searchPlacholder,
            'IMPORTANT_USERS' => $users,
            'LOAD_DATATABLE' => true,
            'LOAD_UPLOT' => true,
            'LOAD_AUTOCOMPLETE' => true,
            'LOAD_ACCEPT_LANGUAGE_PARSER' => true,
            'HTML_FILE' => 'admin/watchlist.html',
            'JS' => 'admin_watchlist.js',
        ];

        return parent::applyPageParams($pageParams);
    }
}
