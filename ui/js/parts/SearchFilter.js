import {fireEvent} from './utils/Event.js?v=2';
import {debounce} from './utils/Functions.js?v=2';

export class SearchFilter {
    constructor() {
        const onSearchInputChange = this.onSearchInputChange.bind(this);
        const debouncedOnSearchInputChange = debounce(onSearchInputChange);
        this.searchField.addEventListener('input', debouncedOnSearchInputChange, false);

        const onDateFilterChanged = this.onDateFilterChanged.bind(this);
        window.addEventListener('dateFilterChanged', onDateFilterChanged, false);
    }

    onSearchInputChange({target}) {
        const value = target.value;
        fireEvent('searchFilterChanged', {query: value});
    }

    onDateFilterChanged() {
        this.searchField.value = '';
    }

    getValue() {
        return this.searchField.value;
    }

    get searchField() {
        return document.getElementById('search');
    }
}
