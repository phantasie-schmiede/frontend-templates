import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psb-modal (even those which are inserted dynamically).
 * This element listens to click events and inserts the response of the href attribute into the target element.
 * The response must be a JSON object with the following structure:
 * {
 *   html: 'Content'
 * }
 *
 * Attributes:
 * - data-psb-ajax-target: Selector of the target element.
 * - data-psb-ajax-mode: Mode of the insertion. Possible values: replace, append, prepend. Default: replace.
 *
 * Examples:
 * Replace the target element with the response:
 * <a href="/content" data-psb-ajax-target="#target">Load</a>
 * Append the response to the target element:
 * <a href="/content" data-psb-ajax-target="#target" data-psb-ajax-mode="append">Load</a>
 * Prepend the response to the target element:
 * <a href="/content" data-psb-ajax-target="#target" data-psb-ajax-mode="prepend">Load</a>
 */
class AjaxContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-ajax-target]';
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
        event.preventDefault();

        let xhr = new XMLHttpRequest();

        xhr.open('GET', event.target.href, true);
        xhr.responseType = 'json';
        xhr.send();
        xhr.onload = this.handleResponse.bind(event.target);
    }

    // this = clicked element
    handleResponse(event) {
        if (event.target.status != 200) {
            console.error(`Error ${event.target.status}: ${event.target.statusText}`);
        } else {
            const content = event.target.response.html;
            const targetElement = document.querySelector(this.dataset.psbAjaxTarget);
            const targetMode = this.dataset.psbAjaxMode ? this.dataset.psbAjaxMode : 'replace';

            if (!targetElement) {
                console.error('Target element not found!');

                return;
            }

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
}

document.addEventListener('DOMContentLoaded', () => {
    new AjaxContent();
});
