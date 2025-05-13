import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class psb-templates-clear-input (even those which are inserted dynamically).
 * This element listens to click events and clears the input field.
 */
class ClearInput {
    constructor() {
        this.postponedElements = [];
        this.registeredElements = [];
        this.selector = '.psb-templates-clear-input';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                /*
                 * If the input is in a collapsed accordion, wait until it is shown to calculate the correct position of
                 * the icon.
                 */
                if (0 === element.clientHeight) {
                    const accordionParent = element.closest('.accordion-collapse');

                    if (null !== accordionParent && !this.postponedElements.includes(element)) {
                        accordionParent.addEventListener('shown.bs.collapse', () => {
                            this.bindEvents();
                        });
                        this.postponedElements.push(element);
                    }

                    return;
                }

                let icon = document.createElement('span');
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">\n' +
                    '  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>\n' +
                    '</svg>';
                icon.classList.add('d-none', 'p-2', 'psb-templates-clear-icon', 'text-secondary');
                icon.style.marginRight = '-0.5rem';
                icon.style.top = (element.clientHeight / 2) + 'px';
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
        const input = event.target.closest('.psb-templates-clear-icon').previousElementSibling;
        input.value = '';
        input.focus();
        input.dispatchEvent(new Event('input'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ClearInput();
});
