import { BaseGrid } from './Base.js?v=2';
import * as Renderers from '../DataRenderers.js?v=2';

export class BotsGrid extends BaseGrid {
    get columnDefs() {
        const columnDefs = [
            {
                className: 'device-id-col',
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
                className: 'yes-no-col',
                targets: 3
            },
        ];

        return columnDefs;
    }

    get columns() {
        const columns = [
            {
                data: 'id',
                render: (data, type, record) => {
                    return Renderers.renderClickableBotId(record);
                }
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
                data: 'modified',
                render: Renderers.renderBoolean
            }
        ];

        return columns;
    }
}
