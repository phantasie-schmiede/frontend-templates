import Rebind from './Modules/rebind.js';
import LoadingSpinnerHelper from './Modules/loadingSpinnerHelper.js';

/*
 * Applies to all elements with the attribute data-psbits-modal (even those which are inserted dynamically).
 * This element listens to click events and opens a modal with the content of the href attribute.
 * The response must be a JSON object with the following structure:
 * {
 *   title: 'Modal title',
 *   body: 'Modal body',
 *   footer: 'Modal footer'
 * }
 * The modal is created dynamically if it does not exist and has the id dynamic-modal.
 *
 * Example:
 * <a href="/modal-content" data-psbits-modal="#dynamic-modal">Open Modal</a>
 */
class DynamicModal {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psbits-modal="#dynamic-modal"]';
        this.modal = null;
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
        const dynamicModalInstance = this;
        let xhr = new XMLHttpRequest();

        event.preventDefault();
        xhr.open('GET', event.currentTarget.href, true);
        xhr.addEventListener('load', (loadEvent) => {
            this.handleResponse.call(dynamicModalInstance, loadEvent);
            LoadingSpinnerHelper.phaseInElements(event.currentTarget);
        });
        xhr.responseType = 'json';
        xhr.send();
    }

    handleResponse(event) {
        if (event.target.status != 200) {
            console.error(`Error ${event.target.status}: ${event.target.statusText}`);
        } else {
            let modalElement = this.getModal();
            let response = event.target.response;

            if (!this.modal) {
                this.modal = new bootstrap.Modal(modalElement);
            }

            modalElement.querySelector('.modal-body').innerHTML = response.body;
            modalElement.querySelector('.modal-footer').innerHTML = response.footer;
            modalElement.querySelector('.modal-title').innerHTML = response.title;
            Rebind.rebindEvents();
            this.modal.show();
        }
    }

    getModal() {
        let modal = document.getElementById("dynamic-modal");

        if (null === modal) {
            return this.createModal();
        }

        return modal;
    }

    createModal() {
        let modal = document.createElement("div");
        modal.classList.add("fade", "modal");
        modal.id = "dynamic-modal";
        modal.setAttribute("aria-hidden", "true");
        modal.setAttribute("aria-labelledby", "dynamic-modal-title");
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1
                            class="modal-title fs-5"
                            id="dynamic-modal-title"></h1>
                        <button
                            aria-label="Close"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        return modal;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DynamicModal();
});
