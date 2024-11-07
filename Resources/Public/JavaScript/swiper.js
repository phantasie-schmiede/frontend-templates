import Rebind from './Modules/rebind.js';

/*
 * Applies to all elements with the class swiper (even those which are inserted dynamically).
 * The elements will be transformed into a swiper slider.
 * The following data attributes are supported:
 * - data-psb-swiper-autoplay: true or false
 * - data-psb-swiper-autoplay-delay: delay in milliseconds
 * - data-psb-swiper-loop: true or false
 * - data-psb-swiper-slides-per-view: number of slides per view
 * - data-psb-swiper-space-between: space between slides in pixels
 * - data-psb-swiper-speed: speed in milliseconds
 *
 * Example:
 * <div
 *     class="swiper"
 *     data-psb-swiper-autoplay="true"
 *     data-psb-swiper-autoplay-delay="7000">
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
                const swiper = new Swiper(swiperContainer, {
                    autoHeight: dataset.psbSwiperAutoHeight,
                    autoplay: dataset.psbSwiperAutoplay === 'true' ? {
                        delay: dataset.psbSwiperAutoplayDelay || 5000,
                    } : false,
                    loop: dataset.psbSwiperLoop === 'true',
                    navigation: dataset.psbSwiperNavigation || {
                        nextEl: dataset.psbSwiperNavigationNextEl || '.swiper-button-next',
                        prevEl: dataset.psbSwiperNavigationPrevEl || '.swiper-button-prev',
                    },
                    pagination: dataset.psbSwiperPagination || {
                        clickable: dataset.psbSwiperPaginationClickable || true,
                        el: dataset.psbSwiperPaginationEl || '.swiper-pagination',
                        type: dataset.psbSwiperPaginationType || 'bullets',
                    },
                    scrollbar: dataset.psbSwiperScrollbar || {
                        draggable: dataset.psbSwiperScrollbarDraggable || true,
                        el: dataset.psbSwiperScrollbarEl || '.swiper-scrollbar',
                    },
                    slidesPerView: dataset.psbSwiperSlidesPerView || 1,
                    spaceBetween: dataset.psbSwiperSpaceBetween || 0,
                    speed: dataset.psbSwiperSpeed || 500,
                });
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PsbSwiper();
});
