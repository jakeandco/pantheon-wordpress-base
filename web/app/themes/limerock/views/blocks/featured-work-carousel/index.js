import { Swiper } from "swiper";
import { Navigation } from "swiper/modules";

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_featured-work-carousel"
  );
  if (!selectedBlocks.length) return;

  const featuredWorkSwiper = new Swiper(".featured-post-swiper", {
    modules: [Autoplay, Navigation],
    direction: "horizontal",
    effect: "slide",
    slidesPerView: "auto",
    // Navigation arrows
    navigation: {
      nextEl: ".featured-work-swiper-button-next",
      prevEl: ".featured-work-swiper-button-prev",
    },
  });
}
