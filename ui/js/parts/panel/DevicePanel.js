import * as Renderers from '../DataRenderers.js?v=2';
import {BasePanel} from './BasePanel.js?v=2';

export class DevicePanel extends BasePanel {

    constructor() {
        let eventParams = {
            //enrichment: true,
            enrichemnt: false,
            type: 'device',
            url: '/admin/deviceDetails',
            cardId: 'device-card',
            panelClosed: 'devicePanelClosed',
            closePanel: 'closeDevicePanel',
            rowClicked: 'deviceTableRowClicked',
        };
        super(eventParams);
    }

    proceedData(data) {
        const browser_name    = (data.browser_name !== null && data.browser_name !== undefined) ? data.browser_name : '';
        const browser_version = (data.browser_version !== null && data.browser_version !== undefined) ? data.browser_version : '';
        const device_record   = {
            ua:             data.ua,
            os_name:        data.os_name,
            device_name:    data.device,
            browser:        `${browser_name} ${browser_version}`,
            lang:           data.lang
        };
        data.device               = Renderers.renderDeviceWithOs(device_record);
        data.browser              = Renderers.renderBrowser(device_record);
        data.lang                 = Renderers.renderLanguage(device_record);
        data.device_created       = Renderers.renderDate(data.created);

        data.ua_modified          = Renderers.renderBoolean(data.modified);
        data.ua                   = Renderers.renderUserAgent(data);

        return data;
    }
}
