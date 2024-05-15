class DynamicModal {
    constructor() {
        this.elements = document.querySelectorAll('[data-bs-target="#dynamicModal"]');
        this.bindEvents();
    }

    bindEvents() {
        this.elements.forEach((element) => {
            element.addEventListener('click', this.handleClick.bind(this));
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
            let modal = this.getModal();
            let response = event.target.response;

            modal.querySelector('.modal-title').textContent = response.title;
            modal.querySelector('.modal-body').innerHTML = response.body;
            modal.querySelector('.modal-footer').innerHTML = response.footer;
        }
    }

    getModal() {
        let modal = document.getElementById("dynamicModal");

        if (null === modal) {
            return createModal();
        }

        return modal;
    }

    createModal() {
        var modal = document.createElement("div");
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
