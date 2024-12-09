import {BasePage} from './Base.js';

import {DatesFilter} from '../parts/DatesFilter.js?v=2';
import {DashboardTiles} from '../parts/DashboardTiles.js?v=2';
import {TopTenGrid} from '../parts/grid/TopTen.js?v=2';

import * as Renderers from '../parts/DataRenderers.js?v=2';

export class DashboardPage extends BasePage {

    constructor() {
        super();

        this.initUi();
    }

    initUi() {
        const getParams = () => {
            const dateRange = datesFilter.getValue();
            return {dateRange};
        };

        const topTenUsersGridParams = {
            getParams: getParams,
            mode: 'mostActiveUsers',
            tableId: 'most-active-users-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableImportantUserWithScoreTile
        };

        const topTenCountriesGridParams = {
            getParams: getParams,
            mode: 'mostActiveCountries',
            tableId: 'most-active-countries-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableCountry
        };

        const topTenResourcesGridParams = {
            getParams: getParams,
            mode: 'mostActiveUrls',
            tableId: 'most-active-urls-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableResourceWithoutQuery
        };

        const topTenIpsWithMostUsersGridParams = {
            getParams: getParams,
            mode: 'ipsWithTheMostUsers',
            tableId: 'ips-with-the-most-users-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableIpWithCountry
        };

        const topTenIpsTorOneGridParams = {
            getParams: getParams,
            mode: 'ipsWithTorOne',
            tableId: 'ips-with-tor-one-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableIpWithCountry
        };

        const topTenUsersWithMostIpsGridParams = {
            getParams: getParams,
            mode: 'usersWithMostIps',
            tableId: 'users-with-most-ips-table',
            dateRangeGrid: true,
            renderItemColumn: Renderers.renderClickableImportantUserWithScoreTile
        };

        const tilesParams = {
            getParams: getParams
        };

        const datesFilter    = new DatesFilter();
        const dashboardTiles = new DashboardTiles(tilesParams);

        const topTenUsersGrid            = new TopTenGrid(topTenUsersGridParams);
        const topTenCountriesGrid        = new TopTenGrid(topTenCountriesGridParams);
        const topTenResourcesGrid        = new TopTenGrid(topTenResourcesGridParams);
        const topTenUsersWithMostIpsGrid = new TopTenGrid(topTenUsersWithMostIpsGridParams);
        const topTenIpsTorOneGrid        = new TopTenGrid(topTenIpsTorOneGridParams);
        const topTenIpsWithMostUsersGrid = new TopTenGrid(topTenIpsWithMostUsersGridParams);
    }
}
