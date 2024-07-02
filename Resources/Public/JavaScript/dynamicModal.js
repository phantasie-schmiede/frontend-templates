import Rebind from './Modules/rebind.js';

class DynamicModal {
    constructor() {
        this.registeredElements = [];
        this.selector = '[data-psb-modal="#dynamicModal"]';
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
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        xhr.open('GET', event.target.href, true);
        xhr.responseType = 'json';
        xhr.send();
        xhr.onload = this.handleResponse.bind(this);
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

            modalElement.querySelector('.modal-title').innerHTML = response.title;
            modalElement.querySelector('.modal-body').innerHTML = response.body;
            modalElement.querySelector('.modal-footer').innerHTML = response.footer;
            Rebind.rebindEvents();
            this.modal.show();
        }
    }

    getModal() {
        let modal = document.getElementById("dynamicModal");

        if (null === modal) {
            return this.createModal();
        }

        return modal;
    }

    createModal() {
        let modal = document.createElement("div");
        modal.classList.add("fade", "modal");
        modal.id = "dynamicModal";
        modal.setAttribute("aria-hidden", "true");
        modal.setAttribute("aria-labelledby", "dynamicModalLabel");
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1
                            class="modal-title fs-5"
                            id="dynamicModalLabel"></h1>
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
