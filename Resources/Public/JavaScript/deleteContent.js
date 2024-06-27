class DeleteContent {
    constructor() {
        this.elements = document.querySelectorAll('[data-psb-delete-target]');
        this.bindEvents();
    }

    bindEvents() {
        this.elements.forEach((element) => {
            element.addEventListener('click', this.handleClick.bind(this));
        });
    }

    handleClick(event) {
        const targetElement = document.querySelector(event.target.dataset.psbDeleteTarget);

        if (!targetElement) {
            console.log('Target element not found!');

            return;
        }

        targetElement.remove();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DeleteContent();
});
