import {BasePage} from './Base.js';

import {DatesFilter} from '../parts/DatesFilter.js?v=2';
import {SearchFilter} from '../parts/SearchFilter.js?v=2';
import {UserGridActionButtons} from '../parts/UserGridActionButtons.js?v=2';
import {ReviewQueueGrid} from '../parts/grid/ReviewQueue.js?v=2';
import {ReviewQueueChart} from '../parts/chart/ReviewQueue.js?v=2';

export class ReviewQueuePage extends BasePage {

    constructor() {
        super();
        this.tableId = 'review-queue-table';
        this.initUi();
    }

    initUi() {
        const chartParams = {
            getParams: function() {
                const mode        = 'review-queue';
                const chartType   = 'line';
                const dateRange   = datesFilter.getValue();
                const searchValue = searchFilter.getValue();

                return {mode, chartType, dateRange, searchValue};
            }
        };

        const gridParams = {
            url     : '/admin/loadReviewQueue',
            tileId  : 'totalUsers',
            tableId : 'review-queue-table',
            dateRangeGrid: true,

            getParams: function() {
                const dateRange   = datesFilter.getValue();
                const searchValue = searchFilter.getValue();

                return {dateRange, searchValue};
            }
        };

        const datesFilter           = new DatesFilter();
        const searchFilter          = new SearchFilter();
        const reviewQueueChart      = new ReviewQueueChart(chartParams);
        const reviewQueueGrid       = new ReviewQueueGrid(gridParams);

        const userGridActionButtons = new UserGridActionButtons(this.tableId);
    }
}
