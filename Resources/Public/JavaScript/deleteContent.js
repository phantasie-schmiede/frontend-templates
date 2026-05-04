import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psbits-delete-target (even those which are inserted dynamically).
 * This element listens to click events and deletes the target element.
 *
 * Attributes:
 * - data-psbits-delete-target: Selector of the target element. Can be set to "self".
 * - data-psbits-delete-mode: Mode of the deletion. Possible values: closest.
 *
 * Examples:
 * Deletes all matching elements:
 * <button data-psbits-delete-target=".delete-me">Delete</button>
 * Deletes the closest matching element:
 * <button data-psbits-delete-target=".delete-me" data-psbits-delete-mode="closest">Delete</button>
 * Deletes the clicked element:
 * <button data-psbits-delete-target="self">Delete</button>
 */
class DeleteContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psbits-delete-target]';
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
        const clickedElement = event.target;
        const data = clickedElement.dataset;
        const mode = data.psbitsDeleteMode;
        const targetSelector = data.psbitsDeleteTarget;

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
