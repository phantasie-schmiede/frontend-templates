import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class psb-templates-upload that contain an input element of type file (even those
 * which are inserted dynamically).
 */
class UploadPreview {
    constructor() {
        this.classes = {
            preview: 'preview',
            removeIcon: 'remove-icon',
        },
        this.registeredElements = [];
        this.selectors = {
            placeholder: '.upload-placeholder',
            preview: '.' + this.classes.preview,
            removeIcon: '.' + this.classes.removeIcon,
            wrapper: '.psb-templates-upload',
            wrapperWithInput: '.psb-templates-upload:has(input[type="file"])',
        };
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selectors.wrapperWithInput).forEach((uploadContainer) => {
            if (!this.registeredElements.includes(uploadContainer)) {
                uploadContainer.querySelector('input[type="file"]').addEventListener('change', this.handleEvent.bind(this));
                this.registeredElements.push(uploadContainer);
            }
        });
    }

    createAudioPreview(url) {
        const preview = document.createElement('audio');

        preview.src = url;
        preview.controls = true;

        return preview;
    }

    createDefaultPreview(file) {
        const preview = document.createElement('div');
        const previewText = document.createElement('span');

        preview.classList.add('d-flex', 'p-2', 'pe-5');
        preview.style.alignItems = 'center';
        preview.style.minHeight = '3rem';

        previewText.textContent = file.name;
        previewText.classList.add('text-break');

        preview.appendChild(previewText);

        return preview;
    }

    createImagePreview(url) {
        const preview = document.createElement('img');

        preview.classList.add('img-fluid');
        preview.src = url;

        return preview;
    }

    createPdfPreview(url) {
        const preview = document.createElement('object');

        preview.data = url;
        preview.style.aspectRatio = '1 / calc(pow(2, 0.5))'; // DIN A4
        preview.type = 'application/pdf';
        preview.width = '100%';
        return preview;
    }

    createPreviewByType(file, type, url) {
        const baseType = type.split('/')[0];

        if ('application/pdf' === type) {
            return this.createPdfPreview(url);
        } else if ('audio' === baseType) {
            return this.createAudioPreview(url);
        } else if ('image' === baseType) {
            return this.createImagePreview(url);
        } else if ('video' === baseType) {
            return this.createVideoPreview(url);
        } else {
            return this.createDefaultPreview(file);
        }
    }

    createVideoPreview(url) {
        const preview = document.createElement('video');

        preview.classList.add('img-fluid');
        preview.src = url;
        preview.controls = true;

        return preview;
    }

    handleEvent(event) {
        const input = event.target;
        const uploadContainer = input.closest(this.selectors.wrapper);
        const uploadPlaceholder = uploadContainer.querySelector(this.selectors.placeholder);

        // Delete old preview image tag
        const oldPreview = uploadContainer.querySelector(this.selectors.preview);
        const oldRemoveIcon = uploadContainer.querySelector(this.selectors.removeIcon);

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

        // Create new preview
        const file = input.files[0];
        const type = file.type;
        const url = URL.createObjectURL(file);
        const preview = this.createPreviewByType(file, type, url);

        preview.classList.add(this.classes.preview);
        preview.onload = () => {
            URL.revokeObjectURL(preview.src);
        };
        uploadPlaceholder.classList.add('d-none');
        uploadContainer.appendChild(preview);

        // Add remove icon
        const removeIcon = document.createElement('span');

        removeIcon.classList.add(this.classes.removeIcon);
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
