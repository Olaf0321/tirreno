import {BasePage} from './Base.js';

import {Map} from '../parts/Map.js?v=2';
import {IpsGrid} from '../parts/grid/Ips.js';
import {IspsGrid} from '../parts/grid/Isps.js?v=2';
import {UsersGrid} from '../parts/grid/Users.js?v=2';
import {EventsGrid} from '../parts/grid/Events.js?v=2';
import {DevicesGrid} from '../parts/grid/Devices.js?v=2';
import {BaseBarChart} from '../parts/chart/BaseBar.js?v=2';
import {StaticTiles} from '../parts/StaticTiles.js?v=2';
import {EventPanel} from '../parts/panel/EventPanel.js?v=2';
import {DevicePanel} from '../parts/panel/DevicePanel.js?v=2';

export class ResourcePage extends BasePage {

    constructor() {
        super();

        this.initUi();
    }

    initUi() {

        const RESOURCE_ID = parseInt(window.location.pathname.replace('/resource/', ''));

        const getParams = () => {
            return {resourceId: RESOURCE_ID};
        };

        const devicesGridParams = {
            url    : '/admin/loadDevices',
            tileId : 'totalDevices',
            tableId: 'devices-table',
            panelType: 'device',
            isSortable: false,

            getParams: getParams
        };

        const eventsGridParams = {
            url    : '/admin/loadEvents',
            tileId : 'totalEvents',
            tableId: 'user-events-table',
            panelType: 'event',
            isSortable: false,

            getParams: getParams
        };

        const ipsGridParams = {
            url    : '/admin/loadIps',
            tileId : 'totalIps',
            tableId: 'ips-table',

            isSortable: false,
            orderByLastseen: true,

            getParams: getParams
        };

        const usersGridParams = {
            url         : '/admin/loadUsers',
            tileId      : 'totalUsers',
            tableId     : 'users-table',

            isSortable: false,

            getParams: getParams
        };

        const ispsGridParams = {
            url       : '/admin/loadIsps',
            tableId   : 'isps-table',
            getParams : getParams,
            isSortable: false
        };

        const mapParams = {
            getParams    : getParams,
            tooltipString: 'event',
            tooltipField : 'total_visit'
        };

        const chartParams = {
            getParams: function() {
                const id        = RESOURCE_ID;
                const mode      = 'resource';
                const chartType = 'bar';

                return {mode, chartType, id};
            }
        };

        const tilesParams = {
            elems: ['totalUsers', 'totalCountries', 'totalIps', 'totalEvents']
        };

        const staticTiles  = new StaticTiles(tilesParams);
        const eventPanel   = new EventPanel();
        const devicePanel  = new DevicePanel();
        const countriesMap = new Map(mapParams);
        const barChart     = new BaseBarChart(chartParams);

        const ipsGrid                   = new IpsGrid(ipsGridParams);
        const ispsGrid                  = new IspsGrid(ispsGridParams);
        const usersGrid                 = new UsersGrid(usersGridParams);
        const eventsGrid                = new EventsGrid(eventsGridParams);
        const devicesGrid               = new DevicesGrid(devicesGridParams);
    }
}
