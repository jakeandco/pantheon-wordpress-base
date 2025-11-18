import { Swiper } from "swiper";
import { Autoplay } from "swiper/modules";
import debounce from "lodash/debounce";

function setFixedHeightForAll(containerSelector) {
  document.querySelectorAll(containerSelector).forEach((sliderEl) => {
    const slides = Array.from(sliderEl.querySelectorAll(".swiper-slide"));
    if (!slides.length) return;

    slides.forEach((s) => {
      s.style.height = "auto";
    });
    sliderEl.style.height = "";

    let maxH = 0;
    slides.forEach((s) => {
      const h = s.offsetHeight;
      if (h > maxH) maxH = h;
    });

    if (maxH > 0) {
      sliderEl.style.height = maxH + "px";
      const wrapper = sliderEl.querySelector(".swiper-wrapper");
      if (wrapper) wrapper.style.height = "100%";
    }
  });
}

export function setup() {
  const selectedBlocks = document.getElementsByClassName(
    "block_homepage-header"
  );

  if (!selectedBlocks.length) return;

  function readyAndMeasure() {
    const doMeasure = () => {
      setFixedHeightForAll(".animating-text-holder");
    };

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(doMeasure).catch(doMeasure);
    } else {
      doMeasure();
    }
  }

  window.addEventListener("load", readyAndMeasure);
  window.addEventListener("resize", debounce(readyAndMeasure, 120));

  const swiper = new Swiper(".animating-text-holder", {
    a11y: {
      enabled: true,
    },
    modules: [Autoplay],
    direction: "vertical",
    effect: "slide",
    slidesPerView: 1,
    loop: true,
    speed: 1000,
    autoplay: {
      delay: 3000,
      reverseDirection: false,
      disableOnInteraction: false,
    },
  });

  window.addEventListener("load", () => {
    try {
      swiper.updateAutoHeight();
    } catch (e) {
      swiper.update();
    }
  });

  window.addEventListener("resize", () => {
    try {
      swiper.updateAutoHeight();
    } catch (e) {
      swiper.update();
    }
  });
}
