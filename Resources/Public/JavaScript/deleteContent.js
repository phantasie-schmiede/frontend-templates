import Rebind from './Modules/rebind.js';

class DeleteContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-delete-target]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents(rootNode = null) {
        if (!rootNode) {
            rootNode = document;
        }

        rootNode.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.addEventListener('click', this.handleClick.bind(this));
                this.registeredElements.push(element);
            }
        });
    }

    handleClick(event) {
        const data = event.target.dataset;
        const mode = document.querySelector(data.psbDeleteMode);
        const targetElement = document.querySelector(data.psbDeleteTarget);

        if (!targetElement) {
            console.log('Target element not found!');

            return;
        }

        if ('closest' === mode) {
            targetElement.closest(targetElement).remove();
        } else {
            targetElement.remove();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DeleteContent();
});
