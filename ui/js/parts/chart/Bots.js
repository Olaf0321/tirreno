import {BaseLineChart} from './BaseLine.js?v=2';
import {COLOR_RED, COLOR_LIGHT_RED} from '../utils/Constants.js?v=2';

export class BotsChart extends BaseLineChart {

    getSeries() {
        const series = [
            {
                label: 'Day',
                scale: 'DAY',
                value: '{YYYY}-{MM}-{DD}'
            },
            {
                label: 'Bots',
                scale: 'EVENTS',
                value: (u, v) => Number(v.toFixed(0)).toLocaleString(),
                points: {
                    space: 0,
                    fill: COLOR_RED,
                },
                stroke: COLOR_RED,
                fill: COLOR_LIGHT_RED
            }
        ];

        return series;
    }
}
