import {BaseLineChart} from './BaseLine.js?v=2';
import {COLOR_GREEN, COLOR_YELLOW, COLOR_LIGHT_GREEN, COLOR_LIGHT_YELLOW, COLOR_RED, COLOR_LIGHT_RED} from '../utils/Constants.js?v=2';

export class UsersChart extends BaseLineChart {
    getSeries() {
        const series = [
            {
                label: 'Day',
                scale: 'DAY',
                value: '{YYYY}-{MM}-{DD}'
            },
            {
                label: 'High trust',
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
                label: 'Average trust',
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
                label: 'On review',
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
