import { BaseGridWithPanel } from './BaseWithPanel.js?v=2';
import * as Renderers from '../DataRenderers.js?v=2';

export class DevicesGrid extends BaseGridWithPanel {
    get columnDefs() {
        const columnDefs = [
            {
                className: 'date-col',
                targets: 0
            },
            {
                className: 'device-type-col',
                targets: 1
            },
            {
                className: 'ua-part-col',
                targets: 2
            },
            {
                className: 'ua-part-col',
                targets: 3
            },
            {
                className: 'lang-col',
                targets: 4
            },
            {
                className: 'yes-no-col',
                targets: 5
            },
            {
                visible: false,
                targets: 6
            },
        ];

        return columnDefs;
    }

    get columns() {
        const columns = [
            {
                data: 'created',
                render: (data, type, record) => {
                    return Renderers.renderDate(data);
                },
            },
            {
                data: 'device',
                render: (data, type, record) => {
                    return Renderers.renderDevice(record);
                }
            },
            {
                data: 'os_name',
                render: (data, type, record) => {
                    return Renderers.renderOs(record);
                }
            },
            {
                data: 'browser_name',
                render: (data, type, record) => {
                    return Renderers.renderBrowser(record);
                }
            },
            {
                data: 'lang',
                render: (data, type, record) => {
                    return Renderers.renderLanguage(record);
                },
            },
            {
                data: 'modified',
                render: Renderers.renderBoolean
            },
            {
                data: 'id',
                name: 'id',
            },
        ];

        return columns;
    }
}
