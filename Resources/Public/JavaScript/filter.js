import Rebind from './Modules/rebind.js';

/*
 * Applies to all input elements with the attribute data-psbits-filter-target (even those which are inserted
 * dynamically). The elements listen to input events and filter a list of target items by the input value. The input
 * value can contain multiple search values separated by spaces. The search is case-insensitive. If the search value
 * starts with a minus or exclamation mark, the search will be inverted. This means that the item will be shown if it
 * does not contain the search value.
 * The items can be filtered by an attribute value referenced by the optional data-psbits-filter-value attribute. If it
 * is not set or no value is defined for data-psbits-filter-value, the text content of the item will be used.
 *
 * If data-psbits-filter-prefer-text is set on the input element, the search will consider both the
 * data-psbits-filter-value attribute and the text content of the item. Elements whose text content matches the search
 * value will be shown in front of elements whose filter value attribute matches the search value.
 *
 * Examples:
 * <input type="text" data-psbits-filter-target="#container .item">
 * <input type="text" data-psbits-filter-target="#container .item" data-psbits-filter-value="data-categories">
 * <input type="text" data-psbits-filter-target="#container .item" data-psbits-filter-value="data-categories" data-psbits-filter-prefer-text>
 *
 * You can display the number of items that match the filter by specifying a selector with the attribute
 * data-psbits-filter-count. Inside the target element, there must be a child element with the attribute
 * data-psbits-filter-count-number which will be updated with the number of matched items. Additionally, you can
 * specify a child element with the attribute data-psbits-filter-count-text which will be updated with the attributes
 * value if at least one item matches the filter. If no items match the filter, the text will be set to its original
 * value.
 *
 * Example:
 * <input type="text" data-psbits-filter-target="#container .item" data-psbits-filter-count="#filter-count">
 * <div id="filter-count">
 *     <span data-psbits-filter-count-number>10</span>
 *     <span data-psbits-filter-count-text="of 10 items found">items in total</span>
 * </div>
 */
class Filter {
    constructor() {
        this.cacheDuration = 3600; // 1 hour
        this.preferTextClass = 'psbits-filter-preferred';
        this.registeredElements = [];
        this.selector = 'input[data-psbits-filter-target]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((input) => {
            if (!this.registeredElements.includes(input)) {
                if (input.dataset.psbitsFilterPreferText) {
                    /*
                     * Store the original order (if set) of the items to restore it later. This is necessary because the
                     * filter might change the order of the items.
                     */
                    document.querySelectorAll(input.dataset.psbitsFilterTarget).forEach((item) => {
                        let order = window.getComputedStyle(item).getPropertyValue('order');

                        if (order && order !== '0') {
                            item.dataset.originalOrder = order;
                        }
                    });
                }

                input.addEventListener('input', this.handleEvent.bind(this));
                this.registeredElements.push(input);
            }

            if ('' === input.value) {
                const cachedValue = JSON.parse(localStorage.getItem('searchitem-' + input.id));

                if (cachedValue && cachedValue.timestamp + this.cacheDuration * 1000 > Date.now()) {
                    input.value = cachedValue.value;
                }
            }

            // Always trigger the input event to ensure the filter is also applied on content changes (e.g. via AJAX).
            input.dispatchEvent(new Event('input'));
        });
    }

    handleEvent(event) {
        const input = event.target;
        const filterValueAttribute = input.dataset.psbitsFilterValue;
        const items = document.querySelectorAll(input.dataset.psbitsFilterTarget);
        const preferText = input.hasAttribute('data-psbits-filter-prefer-text');
        const searchValue = input.value.trim()
        const searchValues = searchValue.toLowerCase().split(' ');

        localStorage.setItem('searchitem-' + input.id, JSON.stringify({value: searchValue, timestamp: Date.now()}));

        items.forEach((item) => {
            let filterValue = null;
            let textContent = item.textContent.toLowerCase();

            if (filterValueAttribute) {
                filterValue = item.getAttribute(filterValueAttribute).toLowerCase();
            }

            if (preferText) {
                // Reset the order for each new seach to ensure the items are sorted correctly.
                item.classList.remove(this.preferTextClass);

                if (item.dataset.originalOrder) {
                    item.style.order = item.dataset.originalOrder;
                } else {
                    item.style.order = '';
                }
            }

            /*
             * If the search value starts with a minus or exclamation mark, the search will be inverted. This means
             * that the item will be shown if the value does not contain the search value.
             * Example: Search value: "-foo", Item value: "foobar" => Item will be hidden.
             */
            if ('' == searchValues || searchValues.every((searchValue) => {
                let negation = false;

                if (searchValue.startsWith('-') || searchValue.startsWith('!')) {
                    negation = true;
                    searchValue = searchValue.substring(1);
                }

                if (preferText && ((!negation && textContent.includes(searchValue)) || (negation && !textContent.includes(searchValue)))) {
                    item.classList.add(this.preferTextClass);
                    item.style.order = '-1'; // Ensure items with matching text are shown first.

                    return true;
                }

                if (filterValueAttribute) {
                    return (!negation && filterValue.includes(searchValue)) || (negation && !filterValue.includes(searchValue));
                }

                // If no filter value is set, use the text content of the item.
                return (!negation && textContent.toLowerCase().includes(searchValue)) || (negation && !textContent.toLowerCase().includes(searchValue));
            })) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });

        // Update the count of visible items if a count selector is set.
        if (input.dataset.psbitsFilterCount) {
            const countElement = document.querySelector(input.dataset.psbitsFilterCount);
            const countNumber = countElement.querySelector('[data-psbits-filter-count-number]');
            const countText = countElement.querySelector('[data-psbits-filter-count-text]');
            const visibleItems = Array.from(items).filter(item => !item.classList.contains('d-none')).length;

            if (countNumber) {
                countNumber.textContent = visibleItems;
            }

            if (countText) {
                // Store the original text content of the count text element if not already done.
                if (!countText.dataset.psbitsFilterCountOriginalText) {
                    countText.dataset.psbitsFilterCountOriginalText = countText.textContent;
                }

                if ('' != searchValues) {
                    countText.textContent = countText.dataset.psbitsFilterCountText || '';
                } else {
                    countText.textContent = countText.dataset.psbitsFilterCountOriginalText || '';
                }
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Filter();
});
