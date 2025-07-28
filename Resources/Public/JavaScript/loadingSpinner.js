import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psbits-spinner (even those which are inserted dynamically).
 * This element listens to click events and shows a loading spinner while the request is being processed.
 */
class LoadingSpinner {
    constructor() {
        this.defaultText = 'Loading...';
        this.registeredElements = [];
        this.selector = '[data-psbits-spinner-text], [data-psbits-spinner-text-hidden]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        let target, trigger;

        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.addEventListener('click', this.handleClickEvent.bind(this));

                this.registeredElements.push(element);
            }
        });
    }

    createInlineSpinner(element) {
        const spinnerElement = document.createElement('span');
        const spinnerText = document.createElement('span');

        spinnerElement.className = 'spinner-border spinner-border-sm text-primary';
        spinnerElement.setAttribute('aria-hidden', 'true');
        spinnerText.setAttribute('role', 'status');

        if (element.dataset.psbitsSpinnerText) {
            element.setAttribute('data-psbits-original-text', element.textContent);
            element.textContent = '';
            spinnerText.textContent = element.dataset.psbitsSpinnerText;
        } else {
            spinnerText.className = 'visually-hidden';
            spinnerText.textContent = element.dataset.psbitsSpinnerTextHidden || this.defaultText;
        }

        element.prepend(spinnerElement, spinnerText);
    }

    handleClickEvent(event) {
        const target = event.target;

        if ('button' === target.tagName.toLowerCase()) {
            if (target.disabled) {
                return; // Prevent action if button is already disabled.
            }

            target.disabled = true; // Disable the button to prevent multiple clicks
        } else {
            if (target.classList.contains('disabled')) {
                return; // Prevent action if element is already disabled.
            }

            target.classList.add('disabled');
        }

        this.createInlineSpinner(target);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new LoadingSpinner();
});
