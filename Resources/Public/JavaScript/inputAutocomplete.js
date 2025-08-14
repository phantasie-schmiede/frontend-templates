import Rebind from './Modules/rebind.js';

/*
 * Applies to all input elements with the attribute data-psbits-autocomplete-items (even those which are inserted
 * dynamically). The elements listen to focus and keydown events and show a list of available options. Options can be
 * selected by clicking on them or by navigating with the arrow keys and confirming with the enter key. Pressing the
 * escape key closes the list without selecting an item.
 *
 * Mandatory attributes:
 * - data-psbits-autocomplete-items: Selector for the container containing the items to show. This container must
 *   contain items with the class psbits-autocomplete-list-item. (This does not have to be a list.) This component
 *   only toggles the visibility of the container (via class "d-none"). No special styling is applied. You can use the
 *   class psbits-autocomplete-list for a predefined style the container.
 *
 * Optional attributes:
 * - data-psbits-autocomplete-preselection: The value must be an HTML escaped JSON array of values that should be
 *   preselected when the items are initialited.
 * - data-psbits-autocomplete-selection-target: Selector for the container where selected items should be added. The
 *   target element must be an empty list (ul or ol). This attribute must be set if multiple items can be selected.
 * - data-psbits-autocomplete-value-target: Selector for the input element where the selected items' values should be
 *   stored (as JSON array). If this attribute is not set, the value of the input element itself will be used.
 *
 * Items:
 * - must have the class psbits-autocomplete-list-item.
 * - can have the attribute data-psbits-autocomplete-value which will be used as the value of the item. If this
 *   attribute is not set, the text content of the item will be used as the value.
 *
 * If only one item should be selected, you should add the class psbits-clear-input to the input element. Without
 * data-psbits-autocomplete-selection-target the input element will be made readonly after the first item is selected.
 * The class psbits-clear-input will add a clear icon to the input field which allows to clear the input and remove
 * the readonly attribute.
 *
 * Combinations:
 * This script uses MutationObserver to bind mouse down events to items that are added dynamically. That way, items
 * can be loaded dynamically via an AJAX request (see ./ajaxContent.js) to improve performance.
 * It is also possible to use this script in combination with ./filter.js. In this case, the input element's value is
 * used to filter the items in the list.
 *
 * Full example:
 * <input
 *     class="psbits-clear-input"
 *     data-psbits-autocomplete-items="#myItemList"
 *     data-psbits-autocomplete-preselection="[&quot;value1&quot;, &quot;value2&quot;]"
 *     data-psbits-autocomplete-selection-target="#mySelectionList"
 *     data-psbits-autocomplete-value-target="#myValueInput"
 *     data-psbits-filter-prefer-text
 *     data-psbits-filter-target="#myItemList"
 *     data-psbits-filter-value="data-psbits-autocomplete-value"
 *     id="myInput"
 *     type="text">
 * <ul
 *     class="d-none list-group psbits-autocomplete-list"
 *     id="myItemList">
 *     <li
 *         class="psbits-autocomplete-list-item"
 *         data-psbits-autocomplete-value="value1">
 *         Item 1
 *     </li>
 *     <li
 *         class="psbits-autocomplete-list-item"
 *         data-psbits-autocomplete-value="value2">
 *         Item 2
 *     </li>
 *     <li
 *         class="psbits-autocomplete-list-item"
 *         data-psbits-autocomplete-value="value3">
 *         Item 3
 *     </li>
 * </ul>
 * <ul
 *     class="psbits-autocomplete-selection-list"
 *     id="mySelectionList">
 *     <!-- Selected items will be added here. -->
 * </ul>
 * <input
 *     id="myValueInput"
 *     type="hidden"
 *     value="">
 *
 * Alternative AJAX version of #myItemList:
 * <ul
 *     class="d-none list-group psbits-autocomplete-list"
 *     data-psbits-ajax-url="/getAutocompleteItems"
 *     id="myItemList">
 *     <!-- Items will be loaded dynamically via AJAX. -->
 * </ul>
 */
class InputAutocomplete {
    constructor() {
        this.activeInput = null;
        this.activeItem = null;
        this.activeItemList = null;
        this.activeSelectionTarget = null;
        this.activeValueTarget = null;
        this.attributes = {
            preselection: 'data-psbits-autocomplete-preselection',
            selected: 'data-psbits-autocomplete-selected',
        }
        this.classes = {
            listItem: 'psbits-autocomplete-list-item',
            listItemActive: 'psbits-autocomplete-list-item__active',
            listItemSelected: 'psbits-autocomplete-list-item__selected',
        };
        this.listItemObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    this.setActiveInput(document.getElementById(mutation.target.getAttribute('data-psbits-autocomplete-related-input')));
                    this.bindMouseDownEvent();
                    this.setPreselection();
                }
            });
        });
        this.registeredElements = [];
        this.selectors = {
            input: 'input[data-psbits-autocomplete-items]',
            listItem: '.' + this.classes.listItem,
            selected: '[' + this.attributes.selected + ']',
        };
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selectors.input).forEach((input) => {
            if (!this.registeredElements.includes(input)) {
                this.setActiveInput(input);
                this.bindMouseDownEvent();
                this.setPreselection();

                /*
                 * It's necessary to add a reference to the input element in the activeItemList so that the
                 * MutationObserver can access it.
                 */
                this.activeItemList.setAttribute('data-psbits-autocomplete-related-input', input.id);
                this.listItemObserver.observe(this.activeItemList, {
                    childList: true,
                });

                input.addEventListener('blur', () => {
                    this.reset();
                });
                input.addEventListener('focus', this.handleFocus.bind(this));
                input.addEventListener('keydown', this.handleKeyDown.bind(this));

                this.registeredElements.push(input);
            }
        });
    }

    bindMouseDownEvent() {
        this.activeItemList.querySelectorAll(this.selectors.listItem).forEach((item) => {
            if (!item.dataset.attachedMousedownEvent) {
                item.addEventListener('mousedown', this.handleItemClick.bind(this));
                item.dataset.attachedMousedownEvent = true;
            }
        });
    }

    getItemByValue(value) {
        const item = this.activeItemList.querySelector(this.selectors.listItem + '[data-psbits-autocomplete-value="' + value + '"]');

        if (item) {
            return item;
        } else {
            // If the item is not found, try to find it by text content.
            const items = this.activeItemList.querySelectorAll(this.selectors.listItem);
            const textContent = value.toString().toLowerCase();

            items.forEach((item) => {
                if (item.textContent.toLowerCase() === textContent) {
                    return item;
                }
            });
        }

        return null;
    }

    handleFocus(event) {
        this.setActiveInput(event.currentTarget);
        this.activeItemList.classList.remove('d-none');
    }

    handleItemClick(event) {
        this.selectItem(event.currentTarget);
    }

    handleKeyDown(event) {
        this.setActiveInput(event.currentTarget);

        // Select active item when enter key is pressed.
        if (13 === event.keyCode) {
            event.preventDefault();

            if (this.activeItem) {
                this.selectItem(this.activeItem);
            }

            return;
        }

        // Close when escape key is pressed.
        if (27 === event.keyCode) {
            this.activeInput.blur();

            return;
        }

        // Navigate with keys up and down and take the visible order of the items into account.
        if (38 === event.keyCode || 40 === event.keyCode) {
            const visibleItems = Array.from(this.activeItemList.querySelectorAll(this.selectors.listItem)).filter(item => item.checkVisibility());
            const itemsOrderedByPositionOnScreen = visibleItems
                .map(el => ({el, rect: el.getBoundingClientRect()}))
                .sort((a, b) => {
                    // Sort from top to bottom first, then from left to right.
                    if (a.rect.top !== b.rect.top) {
                        return a.rect.top - b.rect.top;
                    }

                    return a.rect.left - b.rect.left;
                })
                .map(item => item.el);
            let newIndex = null;

            event.preventDefault();

            /*
             * If no item is currently active or the active element is not visible, select the first visible item if
             * down arrow is pressed, select the last visible item if up arrow is pressed.
             */
            if (38 === event.keyCode && (!this.activeItem || !itemsOrderedByPositionOnScreen.includes(this.activeItem))) {
                newIndex = itemsOrderedByPositionOnScreen.length - 1;
            } else if (40 === event.keyCode && (!this.activeItem || !itemsOrderedByPositionOnScreen.includes(this.activeItem))) {
                newIndex = 0;
            }

            /*
             * If an item is already active, navigate through the visible items. If the active element is on the top
             * and the up arrow is pressed, select the last visible item and vice versa.
             */
            else {
                const currentIndex = itemsOrderedByPositionOnScreen.indexOf(this.activeItem);

                if (38 === event.keyCode) {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : itemsOrderedByPositionOnScreen.length - 1;
                } else if (40 === event.keyCode) {
                    newIndex = currentIndex < itemsOrderedByPositionOnScreen.length - 1 ? currentIndex + 1 : 0;
                }
            }

            this.setActiveItem(itemsOrderedByPositionOnScreen[newIndex]);
        }
    }

    reset() {
        this.activeItemList.classList.add('d-none');
        this.activeInput = null;
        this.activeSelectionTarget = null;
        this.activeValueTarget = null;

        if (this.activeItem) {
            this.activeItem.classList.remove(this.classes.listItemActive);
            this.activeItem = null;
        }
    }

    restoreUnselectedItemByValue(value) {
        const item = this.getItemByValue(value);

        if (item) {
            item.classList.remove('d-none');
            item.removeAttribute(this.attributes.seleted);
        }
    }

    selectItem(item) {
        if (this.activeSelectionTarget) {
            const selectedItem = document.createElement('li');
            const that = this;

            selectedItem.classList.add('d-flex', 'list-group-item', this.classes.listItemSelected);
            selectedItem.setAttribute('data-origin-input', this.activeInput.id);
            selectedItem.innerHTML = item.textContent.trim() + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill ms-auto" viewBox="0 0 16 16">\n' +
                '  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>\n' +
                '</svg>';

            if (item.dataset.psbitsAutocompleteValue) {
                selectedItem.setAttribute('data-value', item.dataset.psbitsAutocompleteValue);
            }

            selectedItem.addEventListener('click', (event) => {
                const value = (event.currentTarget.dataset.value || event.currentTarget.textContent).trim();

                that.setActiveInput(document.getElementById(event.currentTarget.dataset.originInput));
                that.restoreUnselectedItemByValue(value);
                selectedItem.remove();
                that.updateValueTarget();
            });

            item.classList.add('d-none');
            item.setAttribute(this.attributes.seleted, '');
            this.activeSelectionTarget.appendChild(selectedItem);
            this.updateValueTarget();

            if (this.activeItem) {
                this.activeItem.classList.remove(this.classes.listItemActive);
                this.activeItem = null;
            }
        } else {
            this.activeValueTarget.value = item.dataset.psbitsAutocompleteValue || item.textContent;

            if (this.activeInput !== this.activeValueTarget) {
                this.activeInput.value = item.textContent.trim();
            }

            this.activeInput.setAttribute('readonly', 'readonly');
        }
    }

    setActiveInput(input) {
        this.activeInput = input;
        this.activeItemList = document.querySelector(this.activeInput.dataset.psbitsAutocompleteItems);
        this.activeSelectionTarget = document.querySelector(input.dataset.psbitsAutocompleteSelectionTarget) || null;
        this.activeValueTarget = document.querySelector(input.dataset.psbitsAutocompleteValueTarget) || input;
    }

    setActiveItem(item) {
        if (this.activeItem) {
            this.activeItem.classList.remove(this.classes.listItemActive);
        }

        this.activeItem = item;
        this.activeItem.classList.add(this.classes.listItemActive);
    }

    setPreselection() {
        const preselection = this.activeInput.getAttribute(this.attributes.preselection);

        if (!preselection) {
            return;
        }

        const preselectedValues = JSON.parse(preselection);
        let item;
        let unavailableItems = [];

        if (!Array.isArray(preselectedValues)) {
            console.warn('Preselection must be an array of values.');

            return;
        }

        preselectedValues.forEach((value) => {
            item = this.getItemByValue(value);

            if (item) {
                this.selectItem(item);
            } else {
                unavailableItems.push(value);
            }
        });

        /*
         * Update the preselection attribute with the unavailable items. They might be available later, e.g. when the
         * the items are loaded dynamically via AJAX.
         */
        this.activeInput.setAttribute(this.attributes.preselection, JSON.stringify(unavailableItems));
    }

    updateValueTarget() {
        const values = [];

        this.activeSelectionTarget.querySelectorAll('li').forEach((element) => {
            values.push(element.dataset.value || element.textContent);
        });

        this.activeValueTarget.value = JSON.stringify(values);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new InputAutocomplete();
});
