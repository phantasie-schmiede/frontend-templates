import Rebind from './Modules/rebind.js';
import LoadingSpinnerHelper from './Modules/loadingSpinnerHelper.js';

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
            spinnerElement.classList.add('me-1');
            spinnerText.textContent = element.dataset.psbitsSpinnerText;
        } else {
            spinnerText.className = 'visually-hidden';
            spinnerText.textContent = element.dataset.psbitsSpinnerTextHidden || this.defaultText;
        }

        LoadingSpinnerHelper.phaseOutElements(element);
        element.prepend(spinnerElement, spinnerText);
    }

    handleClickEvent(event) {
        const element = event.currentTarget;

        // Disable the element to prevent multiple clicks.
        if ('button' === element.tagName.toLowerCase()) {
            if (element.disabled) {
                event.preventDefault();
                return;
            }

            element.disabled = true;
        } else {
            if (element.classList.contains('disabled')) {
                event.preventDefault();
                return;
            }

            element.classList.add('disabled');
        }

        this.createInlineSpinner(element);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new LoadingSpinner();
});
