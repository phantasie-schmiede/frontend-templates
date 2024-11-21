import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class swiper (even those which are inserted dynamically).
 * The elements will be transformed into a swiper slider.
 * The following data attributes are supported:
 * - data-psb-swiper-configuration: JSON object with the swiper configuration (optional)
 *
 * Example with array:
 * <div
 *     class="swiper"
 *     data-psb-swiper-configuration="{configuration -> f:format.json()}">
 *     <div class="swiper-wrapper">
 *         <div class="swiper-slide">Slide 1</div>
 *         <div class="swiper-slide">Slide 2</div>
 *     </div>
 * </div>
 *
 * Example with inline JSON:
 * <div
 *     class="swiper"
 *     data-psb-swiper-configuration='{
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
                const swiper = new Swiper(swiperContainer, JSON.parse(dataset.psbSwiperConfiguration || '{}'));
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PsbSwiper();
});
