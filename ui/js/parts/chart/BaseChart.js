import {Loader} from '../Loader.js?v=2';
import {getQueryParams}  from '../utils/DataSource.js?v=2';
import {handleAjaxError} from '../utils/ErrorHandler.js?v=2';

export class BaseChart {

    constructor(chartParams) {
        const data  = [];
        const el    = this.chartBlock;
        const opts  = this.getOptions();
        this.loader = new Loader();

        const loaderDiv = document.createElement('div');
        loaderDiv.id = 'loader';
        el.appendChild(loaderDiv);

        this.config = chartParams;

        this.chart = new uPlot(opts, data, el);
        this.reloadData();

        const onDateFilterChanged = this.onDateFilterChanged.bind(this);
        window.addEventListener('dateFilterChanged', onDateFilterChanged, false);
    }

    onDateFilterChanged() {
        this.reloadData();
    }

    stopAnimation() {
        this.loaderBlock.classList.add('is-hidden');
        this.loader.stop();
    }

    updateTimer() {
        this.loaderBlock.classList.remove('is-hidden');
        this.loaderBlock.innerHTML = '<p class="text-loader"></p>';
        const p = this.loaderBlock.querySelector('p');

        this.loader.start(p);
    }

    reloadData() {
        const me = this;

        this.updateTimer();

        const token  = document.head.querySelector('[name=\'csrf-token\'][content]').content;
        const params = this.config.getParams();
        const data   = getQueryParams(params);

        data['mode']  = params.mode;
        data['type']  = params.chartType;
        data['token'] = token;

        $.ajax({
            url: '/admin/loadChart',
            type: 'get',
            scope: me,
            data: data,
            success: me.onChartLoaded,
            error: handleAjaxError,
        });
    }

    onChartLoaded(data, status) {
        if('success' == status) {
            this.scope.stopAnimation();
            this.scope.chart.setData(data);
        }
    }

    formatKiloValue(u, value) {
        if (value === 0) {
            return value;
        }
        if (value % 1000000 === 0) {
            return Math.round(value / 1000000) + 'M';
        }
        if (value % 1000 === 0) {
            return Math.round(value / 1000) + 'k';
        }
        return value;
    }

    get loaderBlock() {
        return document.getElementById('loader');
    }

    get chartBlock() {
        return document.querySelector('.statChart');
    }
}
