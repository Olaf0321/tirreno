import {fireEvent} from './utils/Event.js?v=2';
import {format_ymdThis, addDays} from './utils/Date.js?v=2';
import {debounce} from './utils/Functions.js?v=2';
import {DAYS_IN_RANGE} from './utils/Constants.js?v=2';

export class DatesFilter {

    constructor() {
        this.setupXhrPool();

        if(this.isDateFilterUnavailable) {
            return true;
        }

        if(!this.dateFromField.value && !this.dateToField.value) {
            this.setDefaultDates();
        }

        this.onDateFilterChange = this.onDateFilterChange.bind(this);
        const debouncedOnDateFilterChange = debounce(this.onDateFilterChange);

        this.dateToField.addEventListener('change', debouncedOnDateFilterChange, false);
        this.dateFromField.addEventListener('change', debouncedOnDateFilterChange, false);

        const onIntervalLinkClick = this.onIntervalLinkClick.bind(this);
        this.intervalLinks.forEach( item => item.addEventListener('click', onIntervalLinkClick, false));
    }

    setupXhrPool() {
        // https://gist.github.com/msankhala/3fa2844c1fbad1f4c0185a8e3ef09aed
        // Stop all ajax request by http://tjrus.com/blog/stop-all-active-ajax-requests
        $.xhrPool = []; // array of uncompleted requests
        $.xhrPool.abortAll = function() { // our abort function
            $(this).each(function(idx, jqXHR) {
                jqXHR.abort();
            });
            $.xhrPool.length = 0;
        };

        $.ajaxSetup({
            beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            }
        });
    }

    getDateRange(dateFrom, dateTo, useFromTime) {
        const localDateTo   = this.formatDt(dateTo, true);
        const localDateFrom = this.formatDt(dateFrom, useFromTime);

        return {localDateFrom, localDateTo};
    }

    setDefaultDates() {
        const days     = -DAYS_IN_RANGE;
        const dateTo   = new Date();
        const dateFrom = addDays(dateTo, days);

        // override H:i:s part with 00:00:00 to avoid zero values for first day on chart
        this.setDateRange(dateFrom, dateTo, false);
    }

    onDateFilterChange({target}) {
        $.xhrPool.abortAll();
        fireEvent('dateFilterChanged');
    }

    getValue() {
        const data = {
            dateTo: null,
            dateFrom: null
        };

        if(this.isDateFilterUnavailable) {
            return data;
        }

        data['dateTo']   = this.dateToField.value;
        data['dateFrom'] = this.dateFromField.value;

        const rangeWasChanged = (1 == this.dateFromField.dataset.changed) || (1 == this.dateToField.dataset.changed);
        if(rangeWasChanged) {
            data['keepDates'] = 1;
        }

        return data;
    }

    formatDt(dt, useTime) {
        const dateStr = format_ymdThis(dt, useTime);

        return dateStr;
    }

    onIntervalLinkClick(e) {
        e.preventDefault();

        const link = e.target;
        if(link.classList.contains('active')) {
            return false;
        }

        $.xhrPool.abortAll();

        const type  = link.dataset.type;
        const value = -link.dataset.value;

        let dateTo   = new Date();
        let dateFrom = addDays(dateTo, value);

        if(0 == value) {
            this.clearDateRange();
        } else {
            this.setDateRange(dateFrom, dateTo, false);
        }

        this.intervalLinks.forEach(item => item.classList.remove('active'));
        link.classList.add('active');

        this.onDateFilterChange(e);

        return false;
    }

    setDateRange(dateFrom, dateTo, useFromTime) {
        const {localDateFrom, localDateTo} = this.getDateRange(dateFrom, dateTo, useFromTime);

        this.dateToField.value   = localDateTo;
        this.dateFromField.value = localDateFrom;
    }

    clearDateRange() {
        this.dateToField.value   = null;
        this.dateFromField.value = null;
    }

    get isDateFilterUnavailable() {
        return !this.dateFromField || !this.dateToField;
    }

    get intervalLinks() {
        return this.navbar.querySelectorAll('a');
    }

    get navbar() {
        return document.querySelector('nav.filtersForm.daterange');
    }

    get dateToField() {
        return document.querySelector('input[name="date_to"]');
    }

    get dateFromField() {
        return document.querySelector('input[name="date_from"]');
    }
}
