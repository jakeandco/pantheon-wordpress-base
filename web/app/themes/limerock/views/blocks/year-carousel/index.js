import { Swiper } from "swiper";
import { Navigation } from "swiper/modules";

export function setup() {
  const selectedBlocks = document.getElementsByClassName("block_year-carousel");
  if (!selectedBlocks.length) return;

  const yearNavEl = document.querySelector(".year-swiper-pagination");
  const yearCarouselEl = document.querySelector(".year-carousel");
  const btnNext = document.querySelector(".year-swiper-button-next");
  const btnPrev = document.querySelector(".year-swiper-button-prev");

  function refreshNavButtons(swiper) {
    if (swiper.params && swiper.params.loop) {
      if (btnNext) btnNext.classList.remove("swiper-button-disabled");
      if (btnPrev) btnPrev.classList.remove("swiper-button-disabled");
      return;
    }

    if (btnPrev)
      btnPrev.classList.toggle("swiper-button-disabled", !!swiper.isBeginning);
    if (btnNext)
      btnNext.classList.toggle("swiper-button-disabled", !!swiper.isEnd);
  }

  if (yearNavEl && yearCarouselEl) {
    // NAV (show as horizontal scroller; no loop -> простіша логіка)
    const yearSwiperNav = new Swiper(yearNavEl, {
      slidesPerView: "auto",
      loop: false,
      slideToClickedSlide: true,
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
          document
            .querySelectorAll(".year-swiper-pagination .swiper-slide")
            .forEach((s, idx) =>
              s.classList.toggle("swiper-slide-active", idx === i)
            );
          refreshNavButtons(this);
        },
        init() {
          const i = this.realIndex;
          yearSwiperNav.slideTo(i, 300);
          document
            .querySelectorAll(".year-swiper-pagination .swiper-slide")
            .forEach((s, idx) =>
              s.classList.toggle("swiper-slide-active", idx === i)
            );
          refreshNavButtons(this);
        },
      },
    });

    // Make clicked nav slide control main slider (simple binding)
    yearSwiperNav.on("click", function (swiper, e) {
      const clicked = swiper.clickedIndex;
      if (typeof clicked === "number") {
        yearSwiper.slideTo(clicked, 300);
      }
    });

    // Hook prev/next buttons to both swipers (simple and explicit)
    if (btnNext) {
      btnNext.addEventListener("click", (ev) => {
        ev.preventDefault();
        yearSwiper.slideNext();
      });
    }
    if (btnPrev) {
      btnPrev.addEventListener("click", (ev) => {
        ev.preventDefault();
        yearSwiper.slidePrev();
      });
    }
  }

}
