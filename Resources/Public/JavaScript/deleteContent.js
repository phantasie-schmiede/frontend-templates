import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psb-delete-target (even those which are inserted dynamically).
 * This element listens to click events and deletes the target element.
 *
 * Attributes:
 * - data-psb-delete-target: Selector of the target element. Can be set to "self".
 * - data-psb-delete-mode: Mode of the deletion. Possible values: closest.
 *
 * Examples:
 * Deletes all matching elements:
 * <button data-psb-delete-target=".delete-me">Delete</button>
 * Deletes the closest matching element:
 * <button data-psb-delete-target=".delete-me" data-psb-delete-mode="closest">Delete</button>
 * Deletes the clicked element:
 * <button data-psb-delete-target="self">Delete</button>
 */
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
