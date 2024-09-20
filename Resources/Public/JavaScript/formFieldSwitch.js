import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psb-form-condition-field (even those which are inserted dynamically).
 * This element listens to value changes of another field of the same form and is either shown or hidden.
 *
 * Attributes:
 * - data-psb-form-condition-field: Selector of the field that is listened to.
 * - data-psb-form-condition-mode: Mode of the condition. Possible values: show, hide. Default: show.
 * - data-psb-form-condition-value: Value that triggers the condition. Default: true.
 */
class FormFieldSwitch {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-form-condition-field]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents(rootNode = null) {
        document.querySelectorAll(this.selector).forEach((element) => {
            let conditionField = element.closest('form').querySelector(element.dataset.psbFormConditionField);

            if (!this.registeredElements.includes(element)) {
                conditionField.addEventListener('change', this.handleChange.bind(element));
                this.registeredElements.push(element);
            }
        });
    }

    handleChange(event) {
        const listeningElement = this;
        const data = listeningElement.dataset;
        const mode = data.psbFormConditionMode;
        const targetElement = event.target;

        if (targetElement.value === data.psbFormConditionValue) {
            if ('hide' === mode) {
                listeningElement.classList.add('visually-hidden');
            } else {
                listeningElement.classList.remove('visually-hidden');
            }
        } else {
            if ('hide' === mode) {
                listeningElement.classList.remove('visually-hidden');
            } else {
                listeningElement.classList.add('visually-hidden');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FormFieldSwitch();
});
