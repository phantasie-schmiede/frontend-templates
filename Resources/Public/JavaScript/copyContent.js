class CopyContent {
    constructor() {
        this.elements = document.querySelectorAll('[data-psb-copy-target]');
        this.bindEvents();
    }

    bindEvents() {
        this.elements.forEach((element) => {
            element.addEventListener('click', this.handleClick.bind(this));
        });
    }

    handleClick(event) {
        const data = event.target.dataset;
        const sourceElement = document.querySelector(data.psbCopySource);
        const targetElement = document.querySelector(data.psbCopyTarget);
        const targetMode = data.psbCopyMode ? data.psbCopyMode : 'replace';

        if (!sourceElement || !targetElement) {
            console.error('Source or target element not found!');

            return;
        }

        if ('append' === targetMode) {
            targetElement.append(content);
        } else if ('prepend' === targetMode) {
            targetElement.prepend(content);
        } else {
            targetElement.innerHTML = content;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CopyContent();
});
