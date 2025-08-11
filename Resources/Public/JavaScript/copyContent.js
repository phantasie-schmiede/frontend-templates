import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psbits-copy-target (even those which are inserted dynamically).
 * This element listens to click events and copies the content of the source element to the target element.
 * Attributes:
 * - data-psbits-copy-source: Selector of the source element.
 * - data-psbits-copy-target: Selector of the target element.
 * - data-psbits-copy-mode: Mode of the copy. Possible values: replace, append, prepend. Default: replace.
 *
 * Examples:
 * Replace the target element with the source element:
 * <button data-psbits-copy-source="#source" data-psbits-copy-target="#target">Copy</button>
 * Append the source element to the target element:
 * <button data-psbits-copy-source="#source" data-psbits-copy-target="#target" data-psbits-copy-mode="append">Copy</button>
 * Prepend the source element to the target element:
 * <button data-psbits-copy-source="#source" data-psbits-copy-target="#target" data-psbits-copy-mode="prepend">Copy</button>
 */
class CopyContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psbits-copy-target]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.addEventListener('click', this.handleClick.bind(this));
                this.registeredElements.push(element);
            }
        });
    }

    handleClick(event) {
        const data = event.currentTarget.dataset;
        const sourceElement = document.querySelector(data.psbitsCopySource);
        const targetElement = document.querySelector(data.psbitsCopyTarget);
        const targetMode = data.psbitsCopyMode ? data.psbitsCopyMode : 'replace';

        if (!sourceElement || !targetElement) {
            console.error('Source or target element not found!');

            return;
        }

        // Replace ###uid### with a unique identifier.
        const uniqueId = Date.now();
        let content = sourceElement.innerHTML.replace(/###uid###/g, uniqueId);
        let isAccordionItem = false;

        if (content.includes('_data-bs-target="')) {
            isAccordionItem = true;
        }

        // Some data attributes have to be "hidden" initially to avoid JS errors caused by invalid IDs (...###uid###).
        content = content.replaceAll('_data-', 'data-');

        if ('append' === targetMode) {
            targetElement.insertAdjacentHTML('beforeend', content);
        } else if ('prepend' === targetMode) {
            targetElement.insertAdjacentHTML('afterbegin', content);
        } else {
            targetElement.innerHTML = content;
        }

        // Open new accordion item
        if (isAccordionItem) {
            const newAccordionItemToggle = targetElement.querySelector('[data-bs-target*="' + uniqueId + '"]');

            if (newAccordionItemToggle) {
                newAccordionItemToggle.click();
            }
        }

        Rebind.rebindEvents();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CopyContent();
});
