import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psb-copy-target (even those which are inserted dynamically).
 * This element listens to click events and copies the content of the source element to the target element.
 * Attributes:
 * - data-psb-copy-source: Selector of the source element.
 * - data-psb-copy-target: Selector of the target element.
 * - data-psb-copy-mode: Mode of the copy. Possible values: replace, append, prepend. Default: replace.
 *
 * Examples:
 * Replace the target element with the source element:
 * <button data-psb-copy-source="#source" data-psb-copy-target="#target">Copy</button>
 * Append the source element to the target element:
 * <button data-psb-copy-source="#source" data-psb-copy-target="#target" data-psb-copy-mode="append">Copy</button>
 * Prepend the source element to the target element:
 * <button data-psb-copy-source="#source" data-psb-copy-target="#target" data-psb-copy-mode="prepend">Copy</button>
 */
class CopyContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-copy-target]';
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
        const data = event.target.dataset;
        const sourceElement = document.querySelector(data.psbCopySource);
        const targetElement = document.querySelector(data.psbCopyTarget);
        const targetMode = data.psbCopyMode ? data.psbCopyMode : 'replace';

        if (!sourceElement || !targetElement) {
            console.error('Source or target element not found!');

            return;
        }

        // Replace ###uid### with a unique identifier.
        const content = sourceElement.innerHTML.split('###uid###').join(Date.now());

        if ('append' === targetMode) {
            targetElement.insertAdjacentHTML('beforeend', content);
        } else if ('prepend' === targetMode) {
            targetElement.insertAdjacentHTML('afterbegin', content);
        } else {
            targetElement.innerHTML = content;
        }

        Rebind.rebindEvents();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CopyContent();
});
