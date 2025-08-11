import Rebind from './Modules/rebind.js';

/*
 * Applies to all input elements with the attribute data-psbits-autocomplete (even those which are inserted dynamically).
 * The elements listen to input events and show a list of items filtered by the input value. The input value can
 * contain multiple search values separated by spaces. The search is case-insensitive.
 * The list of items has to be present in the data-psbits-autocomplete attribute as a JSON array.
 *
 * Examples:
 * <input type="text" data-psbits-autocomplete='["Item 1", "Item 2", "Item 3"]'>
 * <input type="text" data-psbits-autocomplete="[&quot;Item 1&quot;, &quot;Item 2&quot;, &quot;Item 3&quot;]'>
 *
 * The items can be simple strings (label and value are identical), but also objects.
 *
 * Examples:
 * <input type="text" data-psbits-autocomplete='[{"label": "Item 1", "value": "item1"}, {"label": "Item 2", "value": "item2"}]'>
 * <input type="text" data-psbits-autocomplete='[{"label": "Item 1", "value": "item1", "searchTerms": "item1, item one"}, {"label": "Item 2", "value": "item2"}]'>
 *
 * If the optional searchTerms attribute is present, the search is performed on the search terms instead of the label.
 * An item can be selected by mouse click or by navigating with the arrow keys and selecting with the enter key.
 * The input value is replaced with the selected item.
 * The list is closed with the escape key, when the input loses focus or when the input value is empty.
 */
class InputAutocomplete {
    constructor() {
        this.activeInput = null;
        this.activeItem = null;
        this.activeItemList = null;
        this.activeSelectionTarget = null;
        this.activeValueTarget = null;
        this.classes = {
            listItemActive: 'psbits-autocomplete-list-item__active',
        };
        this.listItemObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    this.bindMouseDownEvent(mutation.target);
                }
            });
        });
        this.registeredElements = [];
        this.selectors = {
            input: 'input[data-psbits-autocomplete-items]',
            list: '.psbits-autocomplete-list',
            listItem: '.psbits-autocomplete-list-item',
            selected: '[psbits-autocomplete-selected]',
        };
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selectors.input).forEach((input) => {
            if (!this.registeredElements.includes(input)) {
                this.setActiveInput(input);
                this.bindMouseDownEvent(this.activeItemList);
                this.listItemObserver.observe(this.activeItemList, {
                    childList: true,
                });

                input.addEventListener('blur', () => {
                    this.reset();
                });
                input.addEventListener('focus', this.handleFocus.bind(this));
                input.addEventListener('keydown', this.handleKeyDown.bind(this));

                if (0 < this.getPreselectedItems().length) {
                    this.setPreselection();
                }

                this.registeredElements.push(input);
            }
        });
    }

    bindMouseDownEvent(activeItemList) {
        activeItemList.querySelectorAll(this.selectors.listItem).forEach((item) => {
            if (!item.dataset.attachedMousedownEvent) {
                item.addEventListener('mousedown', this.handleItemClick.bind(this));
                item.dataset.attachedMousedownEvent = true;
            }
        });
    }

    getPreselectedItems() {
        return document.querySelectorAll(this.activeInput.dataset.psbitsAutocompleteItems + ' ' + this.selectors.selected);
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
            }
            else if (40 === event.keyCode && (!this.activeItem || !itemsOrderedByPositionOnScreen.includes(this.activeItem))) {
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
        const item = this.activeItemList.querySelector(this.selectors.listItem + '[data-psbits-autocomplete-value="' + value + '"]');

        if (item) {
            item.classList.remove('d-none');
            item.removeAttribute('data-psbits-autocomplete-selected');
        } else {
            // If the item is not found, try to find it by text content.
            const items = this.activeItemList.querySelectorAll(this.selectors.listItem);
            const textContent = value.toLowerCase();

            items.forEach((item) => {
                if (item.textContent.toLowerCase() === textContent) {
                    item.classList.remove('d-none');
                    item.removeAttribute('psbits-autocomplete-selected');

                    return;
                }
            });
        }
    }

    selectItem(item) {
        if (this.activeSelectionTarget) {
            const selectedItem = document.createElement('li');
            const that = this;

            selectedItem.classList.add('d-flex', 'list-group-item', 'psbits-autocomplete-selected-item');
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
            item.setAttribute('data-psbits-autocomplete-selected', '');
            this.activeSelectionTarget.appendChild(selectedItem);
            this.updateValueTarget();

            if (this.activeItem) {
                this.activeItem.classList.remove(this.classes.listItemActive);
                this.activeItem = null;
            }
        } else {
            this.activeValueTarget.value = item.dataset.value || item.textContent;
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
        this.getPreselectedItems().forEach((item) => {
            this.selectItem(item);
        });

        if (this.activeValueTarget) {
            this.updateValueTarget();
        }
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
