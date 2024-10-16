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
                let icon = document.createElement('span');
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">\n' +
                    '  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>\n' +
                    '</svg>';
                icon.classList.add('d-none', 'js-clear-icon', 'text-secondary');
                icon.addEventListener('click', this.handleClick.bind(this));
                element.addEventListener('input', (event) => {
                    if (event.target.value) {
                        icon.classList.remove('d-none');
                    } else {
                        icon.classList.add('d-none');
                    }
                });
                element.insertAdjacentElement('afterend', icon);
                this.registeredElements.push(element);
            }
        });
    }

    handleClick(event) {
        const input = event.target.closest('.js-clear-icon').previousElementSibling;
        input.value = '';
        input.focus();
        input.dispatchEvent(new Event('input'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ClearInput();
});
