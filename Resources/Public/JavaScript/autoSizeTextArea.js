import Rebind from './Modules/rebind.js';

/*
 * Applies to all textarea-elements with the class psbits-auto-size (even those which are inserted dynamically).
 * Those elements listen to input events and get resized automatically.
 */
class AutoSizeTextArea {
    constructor() {
        this.postponedElements = [];
        this.registeredElements = [];
        this.selector = 'textarea.psbits-auto-size';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                // If the text area is in a collapsed accordion, wait until it is shown to calculate the correct height.
                if (0 === element.scrollHeight) {
                    const accordionParent = element.closest('.accordion-collapse');

                    if (null !== accordionParent && !this.postponedElements.includes(element)) {
                        accordionParent.addEventListener('shown.bs.collapse', () => {
                            this.bindEvents();
                        });
                        this.postponedElements.push(element);
                    }

                    return;
                }

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
