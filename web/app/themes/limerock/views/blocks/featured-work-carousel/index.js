import { Swiper } from "swiper";
import { Autoplay } from "swiper/modules";
import { Navigation } from "swiper/modules";

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_featured-work-carousel"
  );
  if (!selectedBlocks.length) return;

  console.log('Featured Work Carousel Block js');
  const featuredWorkSwiper = new Swiper(".featured-post-swiper", {
    modules: [Autoplay, Navigation],
    direction: "horizontal",
    effect: "slide",
    slidesPerView: 1,
    loop: false,
    // Navigation arrows
    navigation: {
      nextEl: ".featured-work-swiper-button-next",
      prevEl: ".featured-work-swiper-button-prev",
    },
  });
}
