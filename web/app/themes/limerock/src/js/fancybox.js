import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { Fancybox } from "@fancyapps/ui";

export function setup() {
    console.log('fancybox');

    Fancybox.bind("[data-fancybox]");

    // document.addEventListener('fancybox:show', () => {
    //   const vids = document.querySelectorAll('video[data-hover-play]');
    //   vids.forEach(v => {
    //     try {
    //       v.pause();
    //       v.currentTime = 0;
    //       v.closest('.block_single-media-item')?.classList.remove('play');
    //     } catch (e) { /* ignore */ }
    //   });
  // });
}

export function teardown() {

}
