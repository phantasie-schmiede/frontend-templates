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
        const uploadContainer = input.closest('.psb-templates-upload');
        const uploadPlaceholder = uploadContainer.querySelector('.upload-placeholder');

        // Delete old preview image tag
        const oldPreview = uploadContainer.querySelector('img');
        const oldRemoveIcon = uploadContainer.querySelector('.remove-icon');

        if (oldPreview) {
            oldPreview.remove();
        }

        if (oldRemoveIcon) {
            oldRemoveIcon.remove();
        }

        if (input.files.length === 0) {
            uploadPlaceholder.classList.remove('d-none');

            return;
        }

        // Create new preview image tag
        const preview = document.createElement('img');

        preview.classList.add('mw-100');
        preview.src = URL.createObjectURL(input.files[0]);
        preview.onload = () => {
            URL.revokeObjectURL(preview.src);
        };
        uploadPlaceholder.classList.add('d-none');
        uploadContainer.appendChild(preview);

        // Add remove icon
        const removeIcon = document.createElement('span');

        removeIcon.classList.add('remove-icon');

        removeIcon.addEventListener('click', () => {
            input.value = '';
            preview.remove();
            removeIcon.remove();
            uploadPlaceholder.classList.remove('d-none');
        });

        uploadContainer.appendChild(removeIcon);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UploadPreview();
});
