import {BasePage} from './Base.js';

import {SearchFilter} from '../parts/SearchFilter.js?v=2';
import {LogbookPanel} from '../parts/panel/LogbookPanel.js?v=2';
import {LogbookGrid} from '../parts/grid/Logbook.js?v=2';

export class LogbookPage extends BasePage {

    constructor() {
        super();

        this.initUi();
    }

    initUi() {
        const gridParams = {
            url    : '/admin/loadLogbook',
            tileId : 'totalLogbook',
            tableId: 'logbook-table',
            panelType: 'logbook',

            sessionGroup: false,
            singleUser: false,
            isSortable: true,

            getParams: function() {
                const dateRange = {dateTo: null, dateFrom: null};
                const searchValue = searchFilter.getValue();

                return {dateRange, searchValue};
            }
        };

        const searchFilter      = new SearchFilter();
        const logbookPanel   = new LogbookPanel();
        const logbookGrid    = new LogbookGrid(gridParams);
    }
}
