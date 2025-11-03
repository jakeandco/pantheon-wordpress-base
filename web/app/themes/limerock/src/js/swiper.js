import { Swiper } from 'swiper';
import { Autoplay } from 'swiper/modules';
import { Navigation, Pagination } from 'swiper/modules';

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
