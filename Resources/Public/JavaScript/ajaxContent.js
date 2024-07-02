import Rebind from './Modules/rebind.js';

class AjaxContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-ajax-target]';
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

            Rebind.rebindEvents(content);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AjaxContent();
});
