import { Swiper } from 'swiper';
import { Autoplay } from 'swiper/modules';
import { Navigation, Pagination, Controller } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/autoplay'; // Import Autoplay CSS
import 'swiper/css/navigation';
import 'swiper/css/pagination';


export function setup() {
  console.log('swiper');

 // debounce helper
  function debounce(fn, ms = 100) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

  function setFixedHeightForAll(containerSelector) {
    document.querySelectorAll(containerSelector).forEach(sliderEl => {
      const slides = Array.from(sliderEl.querySelectorAll('.swiper-slide'));
      if (!slides.length) return;

      slides.forEach(s => {
        s.style.height = 'auto';
      });
      sliderEl.style.height = '';

      let maxH = 0;
      slides.forEach(s => {
        const h = s.offsetHeight;
        if (h > maxH) maxH = h;
      });

      if (maxH > 0) {
        sliderEl.style.height = maxH + 'px';
        const wrapper = sliderEl.querySelector('.swiper-wrapper');
        if (wrapper) wrapper.style.height = '100%';
      }
    });
  }

  function readyAndMeasure() {
    const doMeasure = () => {
      setFixedHeightForAll('.animating-text-holder');
    };

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(doMeasure).catch(doMeasure);
    } else {
      doMeasure();
    }
  }

  window.addEventListener('load', readyAndMeasure);
  window.addEventListener('resize', debounce(readyAndMeasure, 120));

  const swiper = new Swiper('.animating-text-holder', {
    modules: [Autoplay],
    direction: 'vertical',
    effect: 'slide',
    slidesPerView: 1,
    loop: true,
    autoplay: {
      delay: 3000,
      reverseDirection: false,
      disableOnInteraction: false,
    },
  });

  window.addEventListener('load', () => {
    try { swiper.updateAutoHeight(); } catch (e) { swiper.update(); }
  });

  window.addEventListener('resize', () => {
    try { swiper.updateAutoHeight(); } catch (e) { swiper.update(); }
  });

  const captionSwiper = new Swiper('.caption-carousel', {
    modules: [Autoplay, Navigation],
    direction: 'horizontal',
    effect: 'slide',
    slidesPerView: 'auto',
    // Navigation arrows
    navigation: {
      nextEl: '.caption-carousel-button-next',
      prevEl: '.caption-carousel-button-prev',
    },
  });

  const titleItems = document.querySelectorAll('.swiper-titles-pagination .pagination-item');
  const hasCustomPagination = titleItems.length > 0;

  const researchSwiper = new Swiper('.research-swiper', {
    modules: [Autoplay, Navigation, Pagination],
    direction: 'horizontal',
    effect: 'slide',
    slidesPerView: 1,
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    on: {
      slideChange: function () {
        if (hasCustomPagination) {
          updateActiveTitle(this.realIndex);
        }
      },
    },
  });

  // === CUSTOM TITLE NAVIGATION ===
  function updateActiveTitle(activeIndex) {
    if (!hasCustomPagination) return;
    titleItems.forEach((item, i) => {
      item.classList.toggle('is-active', i === activeIndex);
    });
  }

  if (hasCustomPagination) {
    titleItems.forEach((item, i) => {
      item.addEventListener('click', () => {
        researchSwiper.slideToLoop(i);
        updateActiveTitle(i);
      });
    });

    updateActiveTitle(researchSwiper.realIndex);
  }

  const videoSwiper = new Swiper('.video-carousel', {
    modules: [Autoplay, Navigation],
    direction: 'horizontal',
    effect: 'slide',
    slidesPerView: 'auto',
    // Navigation arrows
    navigation: {
      nextEl: '.video-swiper-button-next',
      prevEl: '.video-swiper-button-prev',
    },
  });

  const yearNavEl = document.querySelector('.year-swiper-pagination');
  const yearCarouselEl = document.querySelector('.year-carousel');
  const btnNext = document.querySelector('.year-swiper-button-next');
  const btnPrev = document.querySelector('.year-swiper-button-prev');

  function refreshNavButtons(swiper) {
    if (swiper.params && swiper.params.loop) {
      if (btnNext) btnNext.classList.remove('swiper-button-disabled');
      if (btnPrev) btnPrev.classList.remove('swiper-button-disabled');
      return;
    }

    if (btnPrev) btnPrev.classList.toggle('swiper-button-disabled', !!swiper.isBeginning);
    if (btnNext) btnNext.classList.toggle('swiper-button-disabled', !!swiper.isEnd);
  }

  if (yearNavEl && yearCarouselEl) {
    // NAV (show as horizontal scroller; no loop -> простіша логіка)
    const yearSwiperNav = new Swiper(yearNavEl, {
      slidesPerView: 'auto',
      loop: false,
      slideToClickedSlide: true
    });

    // MAIN
    const yearSwiper = new Swiper(yearCarouselEl, {
      modules: [Navigation],
      slidesPerView: 1,
      loop: false,
      navigation: false,
      on: {
        slideChange() {
          const i = this.realIndex || 0;
          yearSwiperNav.slideTo(i, 0);
          document.querySelectorAll('.year-swiper-pagination .swiper-slide')
            .forEach((s, idx) => s.classList.toggle('swiper-slide-active', idx === i));
          refreshNavButtons(this);
        },
        init() {
          const i = this.realIndex;
          yearSwiperNav.slideTo(i, 300);
          document.querySelectorAll('.year-swiper-pagination .swiper-slide')
            .forEach((s, idx) => s.classList.toggle('swiper-slide-active', idx === i));
          refreshNavButtons(this);
        }
      }
    });

    // Make clicked nav slide control main slider (simple binding)
    yearSwiperNav.on('click', function (swiper, e) {
      const clicked = swiper.clickedIndex;
      if (typeof clicked === 'number') {
        yearSwiper.slideTo(clicked, 300);
      }
    });

    // Hook prev/next buttons to both swipers (simple and explicit)
    if (btnNext) {
      btnNext.addEventListener('click', (ev) => {
        ev.preventDefault();
        yearSwiper.slideNext();
      });
    }
    if (btnPrev) {
      btnPrev.addEventListener('click', (ev) => {
        ev.preventDefault();
        yearSwiper.slidePrev();
      });
    }
  }

  // === PLAY / PAUSE BUTTON ===
  const playPauseBtn = document.querySelector('.swiper-stop-play');
  if (playPauseBtn) {
    let isPlaying = true;
    playPauseBtn.addEventListener('click', () => {
      if (isPlaying) {
        researchSwiper.autoplay.stop();
        playPauseBtn.classList.add('is-paused');
      } else {
        researchSwiper.autoplay.start();
        playPauseBtn.classList.remove('is-paused');
      }
      isPlaying = !isPlaying;
    });
  }
}

export function teardown() {}
