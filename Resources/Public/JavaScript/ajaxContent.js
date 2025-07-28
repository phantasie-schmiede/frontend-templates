import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the attribute data-psbits-ajax-url (even those which are inserted dynamically).
 * This element can listen to click events or fire immediately and inserts the response of the url into the target
 * element.
 * The response must be a JSON object with the following structure:
 * {
 *   html: 'Content'
 * }
 *
 * Attributes:
 * - data-psbits-ajax-mode: Mode of the insertion. Possible values: replace, append, prepend. Default: replace.
 * - data-psbits-ajax-target: Selector of the target element(s). Default: self.
 * - data-psbits-ajax-trigger: Trigger for ajax request. Possible values: click, onload. Default: "onload" if target is self, otherwise "click".
 * - data-psbits-ajax-url: URL to be called.
 *
 * Examples:
 * Replace the content of a container with the response immediately after page load:
 * <div data-psbits-ajax-url="/content">Load</div>
 *
 * Append the response inside the target element on click:
 * <button
 *     data-psbits-ajax-mode="append"
 *     data-psbits-ajax-target="#target"
 *     data-psbits-ajax-url="/content">
 *     Load
 * </button>
 *
 * Prepend the response inside the target elements immediately after page load and on click:
 * <button
 *     data-psbits-ajax-mode="prepend"
 *     data-psbits-ajax-target=".target"
 *     data-psbits-ajax-trigger="click, onload"
 *     data-psbits-ajax-url="/content">
 *     Load
 * </button>
 */
class AjaxContent {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psbits-ajax-url]';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        let target, trigger;

        document.querySelectorAll(this.selector).forEach((element) => {
            if (!this.registeredElements.includes(element)) {
                target = element.dataset.psbAjaxTarget ? element.dataset.psbAjaxTarget : element;
                // Set trigger. If data attribute is set, split by comma and trim. If attribute is not set, default to onload if target is self, otherwise click.
                trigger = element.dataset.psbAjaxTrigger ? element.dataset.psbAjaxTrigger.split(',').map((item) => item.trim()) : (target === element ? ['onload'] : ['click']);

                if (trigger.includes('onload')) {
                    this.sendRequest(element);
                }

                if (trigger.includes('click')) {
                    element.addEventListener('click', this.handleClickEvent.bind(this));
                }

                this.registeredElements.push(element);
            }
        });
    }

    handleClickEvent(event) {
        event.preventDefault();
        this.sendRequest(event.target);
    }

    // this = trigger element
    handleResponse(event) {
        if (event.target.status != 200) {
            console.error(`Error ${event.target.status}: ${event.target.statusText}`);
        } else {
            const content = event.target.response.html;
            const mode = this.dataset.psbAjaxMode ? this.dataset.psbAjaxMode : 'replace';
            const target = this.dataset.psbAjaxTarget ? this.dataset.psbAjaxTarget : this;
            let targetElements;

            if (this === target) {
                targetElements = [this];
            } else {
                targetElements = document.querySelectorAll(target);
            }

            if (0 === targetElements.length) {
                console.error('No target element found!');

                return;
            }

            targetElements.forEach((targetElement) => {
                if ('append' === mode) {
                    targetElement.insertAdjacentHTML('beforeend', content);
                } else if ('prepend' === mode) {
                    targetElement.insertAdjacentHTML('afterbegin', content);
                } else {
                    /*
                     * Check if targetElement contains any active bootstrap modals and hide them before replacing
                     * content. Reopen modals afterwards.
                     */
                    const modals = targetElement.querySelectorAll('.modal.show');
                    const modalIds = Array.from(modals).map(modal => modal.id);

                    modals.forEach((modal) => {
                        const modalInstance = bootstrap.Modal.getInstance(modal);

                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });

                    // Replace the content of the target element
                    targetElement.innerHTML = content;

                    /*
                     * Reopen modals after replacing content. We can't reuse the variable modals here, because the
                     * original modals are not in the DOM anymore.
                     */
                    modalIds.forEach((modalId) => {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(`#${modalId}`);

                        if (modalInstance) {
                            modalInstance.show();
                        }
                    });
                }
            });

            Rebind.rebindEvents();
        }
    }

    sendRequest(element) {
        let xhr = new XMLHttpRequest();

        xhr.open('GET', element.dataset.psbAjaxUrl, true);
        xhr.responseType = 'json';
        xhr.send();
        xhr.onload = this.handleResponse.bind(element);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AjaxContent();
});
