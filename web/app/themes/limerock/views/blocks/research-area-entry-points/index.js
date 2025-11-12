import { Swiper } from "swiper";
import { Autoplay } from "swiper/modules";
import { Navigation, Pagination } from "swiper/modules";

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_research-area-entry-points"
  );

  if (!selectedBlocks.length) return;

  const titleItems = document.querySelectorAll(
    ".swiper-titles-pagination .pagination-item"
  );
  const hasCustomPagination = titleItems.length > 0;

  const researchSwiper = new Swiper(".research-swiper", {
    modules: [Autoplay, Navigation, Pagination],
    direction: "horizontal",
    effect: "slide",
    slidesPerView: 1,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
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
      item.classList.toggle("is-active", i === activeIndex);
    });
  }

  if (hasCustomPagination) {
    titleItems.forEach((item, i) => {
      item.addEventListener("click", () => {
        researchSwiper.slideToLoop(i);
        updateActiveTitle(i);
      });
    });

    updateActiveTitle(researchSwiper.realIndex);
  }

  const playPauseBtn = document.querySelector(".swiper-stop-play");
  if (playPauseBtn) {
    let isPlaying = true;
    playPauseBtn.addEventListener("click", () => {
      if (isPlaying) {
        researchSwiper.autoplay.stop();
        playPauseBtn.classList.add("is-paused");
      } else {
        researchSwiper.autoplay.start();
        playPauseBtn.classList.remove("is-paused");
      }
      isPlaying = !isPlaying;
    });
  }

}
