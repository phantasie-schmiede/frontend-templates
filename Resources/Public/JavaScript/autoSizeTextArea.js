import Rebind from './Modules/rebind.js';

/*
 * Applies to all textarea-elements with the class psb-templates-auto-size (even those which are inserted dynamically).
 * Those elements listen to input events and get resized automatically.
 */
class AutoSizeTextArea {
    constructor() {
        this.registeredElements = [];
        this.selector = 'textarea.psb-templates-auto-size';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                element.style.height = element.scrollHeight + 'px';
                element.style.overflowY = 'hidden';
                element.addEventListener('input', (event) => {
                    event.target.style.height = 'auto';
                    event.target.style.height = (event.target.scrollHeight) + 'px';
                });
                this.registeredElements.push(element);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AutoSizeTextArea();
});
