class AjaxReplacement {
    constructor() {
        this.elements = document.querySelectorAll('[data-psb-target]');
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
            let response = event.target.response;
            let target = document.querySelector(event.target.response.html);

            target.innerHTML = response.html;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AjaxReplacement();
});
