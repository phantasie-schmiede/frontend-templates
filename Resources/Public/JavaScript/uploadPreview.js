import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class psb-templates-upload that contain an input element of type file (even those
 * which are inserted dynamically).
 */
class UploadPreview {
    constructor() {
        this.registeredElements = [];
        this.selector = '.psb-templates-upload:has(input[type="file"])';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((uploadContainer) => {
            if (!this.registeredElements.includes(uploadContainer)) {
                uploadContainer.querySelector('input[type="file"]').addEventListener('change', this.handleEvent.bind(this));
                this.registeredElements.push(uploadContainer);
            }
        });
    }

    handleEvent(event) {
        const input = event.target;

        if (input.files.length === 0) {
            const previewTarget = input.closest('.psb-templates-upload').querySelector('img');

            if (previewTarget) {
                previewTarget.remove();
            }

            input.closest('.psb-templates-upload').querySelector('.upload-placeholder').classList.remove('d-none');
            return;
        }

        // Create preview image tag
        const preview = document.createElement('img');
        preview.src = URL.createObjectURL(input.files[0]);
        preview.onload = () => {
            URL.revokeObjectURL(preview.src);
        };
        input.parentElement.querySelector('.upload-placeholder').classList.add('d-none');
        input.closest('.psb-templates-upload').appendChild(preview);

        // Add remove icon
        const removeIcon = document.createElement('span');
        removeIcon.classList.add('remove-icon');

        removeIcon.addEventListener('click', () => {
            input.value = '';
            preview.remove();
            removeIcon.remove();
            input.closest('.psb-templates-upload').querySelector('.upload-placeholder').classList.remove('d-none');
        });

        input.closest('.psb-templates-upload').appendChild(removeIcon);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UploadPreview();
});
