import Rebind from './Modules/rebind.js';

/*
 * Applies to all input elements with the attribute data-psb-filter-target (even those which are inserted dynamically).
 * The elements listen to input events and filter a list of target items by the input value. The input value can
 * contain multiple search values separated by spaces. The search is case-insensitive.
 * The items will be filtered by an attribute value referenced by the optional data-psb-filter-value attribute. If no
 * value is defined for data-psb-filter-value, the text content of the item will be used.
 *
 * Examples:
 * <input type="text" data-psb-filter-target=".item">
 * <input type="text" data-psb-filter-target=".item" data-psb-filter-value="data-categories">
 */
class Filter {
    constructor() {
        this.cacheDuration = 3600; // 1 hour
        this.registeredElements = [];
        this.selector = 'input[data-psb-filter-target]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((input) => {
            if (!this.registeredElements.includes(input)) {
                input.addEventListener('input', this.handleEvent.bind(this));
                this.registeredElements.push(input);
            }

            if ('' === input.value) {
                const cachedValue = JSON.parse(localStorage.getItem('searchitem-' + input.id));

                if (cachedValue && cachedValue.timestamp + this.cacheDuration * 1000 > Date.now()) {
                    input.value = cachedValue.value;
                }

                input.dispatchEvent(new Event('input'));
            }
        });
    }

    handleEvent(event) {
        const input = event.target;
        const filterValue = input.dataset.psbFilterValue;
        const items = document.querySelectorAll(input.dataset.psbFilterTarget);
        const searchValue = input.value.trim()
        const searchValues = searchValue.toLowerCase().split(' ');

        localStorage.setItem('searchitem-' + input.id, JSON.stringify({value: searchValue, timestamp: Date.now()}));

        items.forEach((item) => {
            let value = '';

            if (filterValue) {
                value = item.getAttribute(filterValue);
            } else {
                value = item.textContent;
            }

            if (searchValues.every((searchValue) => value.toLowerCase().includes(searchValue))) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Filter();
});
