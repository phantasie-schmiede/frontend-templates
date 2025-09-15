import Rebind from './Modules/rebind.js';
import LoadingSpinnerHelper from './Modules/loadingSpinnerHelper.js';

/*
 * Applies to all elements with the attribute data-psbits-spinner-text or data-psbits-spinner-text-hidden (even those
 * which are inserted dynamically). This element listens to click events and shows a loading spinner inside the clicked
 * element while the request is being processed.
 *
 * Attributes:
 * data-psbits-spinner-text: Text to be displayed next to the spinner.
 * data-psbits-spinner-text-hidden: Text that is visually hidden but used for screen readers.
 *
 * It is possible to hide content inside the element while the spinner is shown by adding the attribute
 * data-psbits-spinner-phase-out to the element (all content is hidden) or any of its children. The original content
 * will be restored after operations of ajaxContent.js or dynamicModal.js are finished.
 *
 * Example 1 (spinner will show in front of the button text "Click me"):
 * <button
 *     class="btn btn-primary"
 *     data-psbits-spinner-text-hidden="Loading..."
 *     type="button">
 *     Click me
 * </button>
 *
 * Example 2 (The button text will be replaced by the spinner and the text "Loading..."):
 * <button
 *    class="btn btn-primary"
 *    data-psbits-spinner-text="Loading..."
 *    data-psbits-spinner-phase-out
 *    type="button">
 *    Click me
 * </button>
 */
class LoadingSpinner {
    constructor() {
        this.defaultText = 'Loading ...';
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
        const isSubmitButton = 'button' === element.tagName.toLowerCase() && 'submit' === element.type && element.form;

        // Check if the element is inside a form and form validation is successful.
        if (isSubmitButton && !element.form.checkValidity()) {
            // If the form is invalid, let the browser show the native error messages.
            return;
        }

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

        /*
         * Submit form if the element is a form submit button. This is necessary because the element is disabled before
         * the form is submitted.
         */
        if (isSubmitButton) {
            element.form.requestSubmit(element);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new LoadingSpinner();
});
