import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class swiper (even those which are inserted dynamically).
 * The elements will be transformed into a swiper slider.
 * The following data attributes are supported:
 * - data-psbits-swiper-configuration: JSON object with the swiper configuration (optional)
 *
 * Example with array:
 * <div
 *     class="swiper"
 *     data-psbits-swiper-configuration="{configuration -> f:format.json()}">
 *     <div class="swiper-wrapper">
 *         <div class="swiper-slide">Slide 1</div>
 *         <div class="swiper-slide">Slide 2</div>
 *     </div>
 * </div>
 *
 * Example with inline JSON:
 * <div
 *     class="swiper"
 *     data-psbits-swiper-configuration='{
 *         "slidesPerView": 1,
 *         "spaceBetween": 10,
 *         "loop": true,
 *         "pagination": {
 *             "el": ".swiper-pagination",
 *             "clickable": true
 *         }
 *     }'>
 *     <div class="swiper-wrapper">
 *         <div class="swiper-slide">Slide 1</div>
 *         <div class="swiper-slide">Slide 2</div>
 *     </div>
 * </div>
 */
class PsbSwiper {
    constructor() {
        this.registeredElements = [];
        this.selector = '.swiper';
        this.bindEvents();
        Rebind.registerBindFunction(this.bindEvents.bind(this));
    }

    bindEvents() {
        document.querySelectorAll(this.selector).forEach((swiperContainer) => {
            if (!this.registeredElements.includes(swiperContainer)) {
                const dataset = swiperContainer.dataset;
                const configuration = JSON.parse(dataset.psbitsSwiperConfiguration || '{}');
                const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
                if (reducedMotion.matches) {
                    configuration.autoplay = false;
                    configuration.speed = 0;
                }
                const swiper = new Swiper(swiperContainer, configuration);
                swiperContainer.addEventListener('keydown', (event) => {
                    if (event.target !== swiperContainer || !['ArrowLeft', 'ArrowRight'].includes(event.key)) {
                        return;
                    }
                    event.preventDefault();
                    if ('ArrowRight' === event.key) {
                        swiper.slideNext();
                    } else {
                        swiper.slidePrev();
                    }
                });
                if (configuration.autoplay) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-dark swiper-autoplay-toggle';
                    const updateLabel = () => {
                        button.textContent = swiper.autoplay.running ? dataset.psbitsSwiperPauseLabel : dataset.psbitsSwiperResumeLabel;
                    };
                    button.addEventListener('click', () => {
                        if (swiper.autoplay.running) {
                            swiper.autoplay.stop();
                        } else {
                            swiper.autoplay.start();
                        }
                        updateLabel();
                    });
                    swiperContainer.after(button);
                    updateLabel();
                    reducedMotion.addEventListener('change', (event) => {
                        if (event.matches) {
                            swiper.autoplay.stop();
                            swiper.params.speed = 0;
                            updateLabel();
                        }
                    });
                }
                this.registeredElements.push(swiperContainer);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PsbSwiper();
});
