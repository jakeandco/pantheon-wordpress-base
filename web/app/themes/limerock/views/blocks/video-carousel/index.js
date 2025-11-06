import { Swiper } from "swiper";
import { Autoplay } from "swiper/modules";
import { Navigation, Pagination, Controller } from "swiper/modules";
import debounce from "lodash/debounce";

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_video-carousel"
  );
  if (!selectedBlocks.length) return;

  const videoSwiper = new Swiper(".video-carousel", {
    modules: [Autoplay, Navigation],
    direction: "horizontal",
    effect: "slide",
    slidesPerView: "auto",
    // Navigation arrows
    navigation: {
      nextEl: ".video-swiper-button-next",
      prevEl: ".video-swiper-button-prev",
    },
  });
}
