import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class psb-templates-upload that contain an input element of type file (even those
 * which are inserted dynamically).
 */
class UploadPreview {
    constructor() {
        this.classes = {
            preview: 'swiper-slide',
            removeIcon: 'remove-icon',
        },
        this.registeredElements = [];
        this.files = [];
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

    createPreviewByType(file, input, swiper, type, uploadContainer, url) {
        const baseType = type.split('/')[0];
        let slideContent;

        if ('application/pdf' === type) {
            slideContent = this.createPdfPreview(url);
        } else if ('audio' === baseType) {
            slideContent = this.createAudioPreview(url);
        } else if ('image' === baseType) {
            slideContent = this.createImagePreview(url);
        } else if ('video' === baseType) {
            slideContent = this.createVideoPreview(url);
        } else {
            slideContent = this.createDefaultPreview(file);
        }

        const previewSlide = document.createElement('div');

        previewSlide.appendChild(slideContent);
        previewSlide.classList.add(this.classes.preview);
        previewSlide.onload = () => {
            URL.revokeObjectURL(preview.src);
        };

        // Add remove icon
        const removeIcon = document.createElement('span');

        removeIcon.classList.add(this.classes.removeIcon);
        removeIcon.addEventListener('click', () => {
            const dataTransfer = new DataTransfer();
            const files = this.files[input.id];
            const indexToDelete = swiper.slides.indexOf(previewSlide);

            // Delete index from files array
            this.files[input.id] = files.filter((file, index) => index !== indexToDelete);

            // Add files to dataTransfer
            this.files[input.id].forEach(file => dataTransfer.items.add(file));

            // Update file input
            input.files = dataTransfer.files;

            // Remove preview
            swiper.removeSlide(indexToDelete);
        });

        previewSlide.appendChild(removeIcon);

        return previewSlide;
    }

    createVideoPreview(url) {
        const preview = document.createElement('video');

        preview.classList.add('img-fluid');
        preview.src = url;
        preview.controls = true;

        return preview;
    }

    getSwiper(id, parent) {
        const existingSwiper = document.querySelector('#' + id);

        if (null !== existingSwiper) {
            return existingSwiper.swiper;
        }

        parent.insertAdjacentHTML('afterend', `
            <div class="mt-3 swiper" id="${id}">
                <div class="swiper-wrapper"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        `);

        const newSwiper = new Swiper('#' + id, {
            autoHeight: true,
            breakpoints: {
                576: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 40
                }
            },
            slidesPerView: 1,
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        return newSwiper;
    }

    handleEvent(event) {
        const input = event.target;
        const swiperId = 'swiper-' + input.id;
        const uploadContainer = input.closest(this.selectors.wrapper);
        const uploadPlaceholder = uploadContainer.querySelector(this.selectors.placeholder);

        const swiper = this.getSwiper(swiperId, uploadContainer);
        // Store files in a property to merge them with later selected files
        if (!this.files[input.id]) {
            this.files[input.id] = [];
        }

        const dataTransfer = new DataTransfer();
        const files = this.files[input.id];

        for (let index = 0; index < input.files.length; index++) {
            let file = input.files[index];
            let type = file.type;
            let url = URL.createObjectURL(file);
            let preview = this.createPreviewByType(file, input, swiper, type, uploadContainer, url);

            dataTransfer.items.add(file);
            this.files[input.id].push(file);
            swiper.appendSlide(preview);
        }

        // Update file input
        input.files = dataTransfer.files;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UploadPreview();
});
