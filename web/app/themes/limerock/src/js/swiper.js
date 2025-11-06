import { setup as homepageHeader } from "@blocks/homepage-header";
import { setup as photoAndCaptionCarousel } from "@blocks/photo-and-caption-carousel";
import { setup as researchAreaEntryPoints } from "@blocks/research-area-entry-points";
import { setup as videoCarousel } from "@blocks/video-carousel";
import { setup as yearCarousel } from "@blocks/year-carousel";

import "swiper/css";
import "swiper/css/autoplay"; // Import Autoplay CSS
import "swiper/css/navigation";
import "swiper/css/pagination";

export function setup() {
  try {
    photoAndCaptionCarousel();
  } catch (e) {
    console.warn("Error in @blocks/photo-and-caption-carousel/index.js");
  }
  try {
    homepageHeader();
  } catch (e) {
    console.warn("Error in @blocks/homepage-header/index.js", e);
  }
  try {
    researchAreaEntryPoints();
  } catch (e) {
    console.warn("Error in @blocks/research-area-entry-points/index.js", e);
  }
  try {
    videoCarousel();
  } catch (e) {
    console.warn("Error in @blocks/video-carousel/index.js", e);
  }
  try {
    yearCarousel();
  } catch (e) {
    console.warn("Error in @blocks/year-carousel/index.js", e);
  }
}

export function teardown() {}
