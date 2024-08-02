import Rebind from './Modules/rebind.js';

/*
 * Applies to all input elements with the attribute data-psb-autocomplete (even those which are inserted dynamically).
 * The elements listen to input events and show a list of items filtered by the input value. The input value can
 * contain multiple search values separated by spaces. The search is case-insensitive.
 * The list of items has to be present in the data-psb-autocomplete attribute as a JSON array.
 *
 * Example:
 * <input type="text" data-psb-autocomplete='["Item 1", "Item 2", "Item 3"]'>
 *
 * An item can be selected by mouse click or by navigating with the arrow keys and selecting with the enter key.
 * The input value is replaced with the selected item.
 * The list is closed with the escape key, when the input loses focus or when the input value is empty.
 */
class InputAutocomplete {
    constructor() {
        this.activeInput = null;
        this.activeItem = null;
        this.activeSelectionTarget = null;
        this.activeValueTarget = null;
        this.classes = {
            list: ['autocomplete-list', 'list-group'],
            listItem: ['autocomplete-list-item', 'list-group-item'],
            listItemActive: 'autocomplete-list-item__active',
        };
        this.eventListenerFunctions = {};
        this.list = null;
        this.registeredElements = [];
        this.selectors = {
            input: 'input[data-psb-autocomplete-items]',
            list: '.autocomplete-list',
            listItem: '.autocomplete-list-item',
        };
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selectors.input).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.addEventListener('click', this.handleEvent.bind(this));
                element.addEventListener('input', this.handleEvent.bind(this));
                this.registeredElements.push(element);
            }
        });
    }

    handleEvent(event) {
        this.resetAll();
        this.activeInput = event.target;

        const value = this.activeInput.value;

        if (0 === value.length) {
            return;
        }

        if (this.activeInput.dataset.psbAutocompleteSelectionTarget && this.activeInput.dataset.psbAutocompleteValueTarget) {
            this.activeSelectionTarget = document.querySelector(this.activeInput.dataset.psbAutocompleteSelectionTarget);
            this.activeSelectionTarget.setAttribute('data-psb-autocomplete-value-target', this.activeInput.dataset.psbAutocompleteValueTarget);
            this.activeValueTarget = document.querySelector(this.activeInput.dataset.psbAutocompleteValueTarget);
        }

        const items = JSON.parse(this.activeInput.dataset.psbAutocompleteItems);
        const searchValues = value.split(' ');
        let alreadySelectedItems = [];

        if (this.activeValueTarget && this.activeValueTarget.value) {
            alreadySelectedItems = JSON.parse(this.activeValueTarget.value);
        }

        this.list = document.createElement('ul');
        this.list.classList.add(...this.classes.list);
        this.list.style.width = `${this.activeInput.offsetWidth}px`;

        items.forEach((item) => {
            let label, value;

            if ('string' !== typeof item && ('object' !== typeof item || !item.hasOwnProperty('label') || !item.hasOwnProperty('value'))) {
                return;
            }

            if ('object' === typeof item && item.hasOwnProperty('label')) {
                label = item.label;
                value = item.value;
            } else {
                label = item;
            }

            // Check if all search values are included in the label and if item is not already selected.
            if (!alreadySelectedItems.includes(value.toString()) && searchValues.every((searchValue) => label.toLowerCase().includes(searchValue.toLowerCase()))) {
                const listItem = document.createElement('li');

                listItem.classList.add(...this.classes.listItem);
                listItem.textContent = label;
                listItem.addEventListener('mousedown', this.handleItemClick.bind(this));

                if (value) {
                    listItem.setAttribute('data-value', value);
                }

                this.list.appendChild(listItem);
            }
        });

        this.activeInput.insertAdjacentElement('afterend', this.list);

        this.eventListenerFunctions = {
            blur: this.resetAll.bind(this), focus: this.handleEvent.bind(this), keydown: this.handleKeyDown.bind(this),
        };

        this.activeInput.addEventListener('blur', this.eventListenerFunctions.blur);
        this.activeInput.addEventListener('focus', this.eventListenerFunctions.focus);
        this.activeInput.addEventListener('keydown', this.eventListenerFunctions.keydown);

    }

    handleItemClick(event) {
        this.selectItem(event.target);
    }

    handleKeyDown(event) {
        // Select active or first item when enter key is pressed.
        if (13 === event.keyCode) {
            event.preventDefault();

            if (!this.activeItem) {
                const firstItem = this.list.querySelector(this.selectors.listItem);

                if (firstItem) {
                    this.setActive(firstItem);
                }
            }

            this.selectItem(this.activeItem);
        }

        // Close when escape key is pressed.
        if (27 === event.keyCode) {
            this.resetAll();
        }

        // Focus previous or last item when up key is pressed.
        if (38 === event.keyCode) {
            if (!this.activeItem || this.activeItem === this.list.querySelector(this.selectors.listItem)) {
                const lastItem = this.list.querySelector(this.selectors.listItem + ':last-child');

                if (lastItem) {
                    this.setActive(lastItem);
                }
            } else {
                this.setActive(this.activeItem.previousElementSibling);
            }
        }

        // Focus next or first item when down key is pressed.
        if (40 === event.keyCode) {
            if (!this.activeItem || this.activeItem === this.list.querySelector(this.selectors.listItem + ':last-child')) {
                const firstItem = this.list.querySelector(this.selectors.listItem);

                if (firstItem) {
                    this.setActive(firstItem);
                }
            } else {
                this.setActive(this.activeItem.nextElementSibling);
            }
        }
    }

    resetAll() {
        if (this.activeInput) {
            this.activeInput.removeEventListener('blur', this.eventListenerFunctions.blur);
            this.activeInput.removeEventListener('focus', this.eventListenerFunctions.focus);
            this.activeInput.removeEventListener('keydown', this.eventListenerFunctions.keydown);
            this.activeInput = null;
            this.activeSelectionTarget = null;
            this.activeValueTarget = null;
        }

        this.activeItem = null;
        this.eventListenerFunctions = {};

        if (this.list) {
            this.list.parentNode.removeChild(this.list);
            this.list = null;
        }
    }

    selectItem(item) {
        if (this.activeSelectionTarget) {
            const selectedItem = document.createElement('li');

            selectedItem.classList.add('list-group-item');
            selectedItem.textContent = item.textContent;

            selectedItem.addEventListener('click', (event) => {
                const list = event.target.parentNode;
                const valueTarget = document.querySelector(list.dataset.psbAutocompleteValueTarget);

                selectedItem.remove();
                this.updateValue(list, valueTarget);
            });

            if (item.dataset.value) {
                selectedItem.setAttribute('data-value', item.dataset.value);
            }

            this.activeSelectionTarget.appendChild(selectedItem);
            this.updateValue(this.activeSelectionTarget, this.activeValueTarget);
            this.activeInput.value = '';
        } else {
            this.activeInput.value = item.textContent;
        }

        this.resetAll();
    }

    setActive(element) {
        if (this.activeItem) {
            this.activeItem.classList.remove(this.classes.listItemActive);
        }

        this.activeItem = element;
        this.activeItem.classList.add(this.classes.listItemActive);
    }

    /*
     * Target elements have to be passed as arguments because they are not always present (the list is resetted before
     * you can remove an item)!
     */
    updateValue(selectionTarget, valueTarget) {
        if (selectionTarget && valueTarget) {
            const values = [];

            selectionTarget.querySelectorAll('li').forEach((element) => {
                values.push(element.dataset.value ? element.dataset.value : element.textContent);
            });

            valueTarget.value = JSON.stringify(values);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new InputAutocomplete();
});
