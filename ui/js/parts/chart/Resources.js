import {BaseLineChart} from './BaseLine.js?v=2';
import {COLOR_RED, COLOR_GREEN, COLOR_YELLOW, COLOR_LIGHT_GREEN, COLOR_LIGHT_RED, COLOR_LIGHT_YELLOW} from '../utils/Constants.js?v=2';

export class ResourcesChart extends BaseLineChart {

    getSeries() {
        const series = [
            {
                label: 'Day',
                scale: 'DAY',
                value: '{YYYY}-{MM}-{DD}'
            },
            {
                label: '200',
                scale: 'EVENTS',
                value: (u, v) => Number(v.toFixed(0)).toLocaleString(),
                points: {
                    space: 0,
                    fill: COLOR_GREEN,
                },
                stroke: COLOR_GREEN,
                fill: COLOR_LIGHT_GREEN
            },
            {
                label: '404',
                scale: 'EVENTS',
                value: (u, v) => Number(v.toFixed(0)).toLocaleString(),
                points: {
                    space: 0,
                    fill: COLOR_YELLOW,
                },
                stroke: COLOR_YELLOW,
                fill: COLOR_LIGHT_YELLOW
            },
            {
                label: '403 & 500',
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
