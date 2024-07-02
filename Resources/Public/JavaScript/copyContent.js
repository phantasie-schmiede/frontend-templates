import Rebind from './Modules/rebind.js';

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

        const content = sourceElement.innerHTML;

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
