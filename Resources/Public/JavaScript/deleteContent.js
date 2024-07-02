import Rebind from './Modules/rebind.js';

class DeleteContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-delete-target]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents(rootNode = null) {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.addEventListener('click', this.handleClick.bind(this));
                this.registeredElements.push(element);
            }
        });
    }

    handleClick(event) {
        const clickedElement = event.target;
        const data = clickedElement.dataset;
        const mode = data.psbDeleteMode;
        const targetSelector = data.psbDeleteTarget;

        if ('self' === targetSelector) {
            clickedElement.remove();
        } else if ('closest' === mode) {
            clickedElement.closest(targetSelector).remove();
        } else {
            document.querySelectorAll(targetSelector).forEach((element) => {
                element.remove();
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DeleteContent();
});
