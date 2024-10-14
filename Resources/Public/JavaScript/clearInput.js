import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class js-clear-input (even those which are inserted dynamically).
 * This element listens to click events and clears the input field.
 */
class ClearInput {
    constructor() {
        this.registeredElements = [];
        this.selector = '.js-clear-input';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                let button = document.createElement('button');
                button.innerHTML = 'X';
                button.classList.add('clear-input d-none');
                button.addEventListener('click', this.handleClick.bind(this));
                element.addEventListener('input', (event) => {
                    if (event.target.value) {
                        button.classList.remove('d-none');
                    } else {
                        button.classList.add('d-none');
                    }
                });
                element.insertAdjacentElement('afterend', button);
                this.registeredElements.push(element);
            }
        });
    }

    handleClick(event) {
        const input = event.target.previousElementSibling;
        input.value = '';
        input.focus();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ClearInput();
});
