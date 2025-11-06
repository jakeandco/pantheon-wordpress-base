import { Swiper } from "swiper";
import { Autoplay } from "swiper/modules";
import { Navigation } from "swiper/modules";

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_photo-and-caption-carousel"
  );

  if (!selectedBlocks.length) return;

  const captionSwiper = new Swiper(".caption-carousel", {
    modules: [Autoplay, Navigation],
    direction: "horizontal",
    effect: "slide",
    slidesPerView: "auto",
    // Navigation arrows
    navigation: {
      nextEl: ".caption-carousel-button-next",
      prevEl: ".caption-carousel-button-prev",
    },
  });
}
