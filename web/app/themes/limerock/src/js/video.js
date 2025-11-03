export function setup() {
  let hoverVideos = document.querySelectorAll('[data-hover-play]');

   hoverVideos.forEach(video => {
    const block = video.closest('figure');
    const block2 = video.closest('.block_single-media-item--full');
    const block3 = video.closest('.block_single-media-item--wide');
    const durationSpan = block?.querySelector('.video-duration');
    if( ! block2 && ! block3 ) {
      block.addEventListener('mouseenter', () => {
        video.play();
      });

      block.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
      });
    } else if( ! block2 ) {
      block3.addEventListener('mouseenter', () => {
        video.play();
      });

      block3.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
      });
    } else {
      block2.addEventListener('mouseenter', () => {
        video.play();
      });

      block2.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
      });
    }


     video.addEventListener('loadedmetadata', () => {
      if (durationSpan) {
        const totalSeconds = Math.floor(video.duration);
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        durationSpan.textContent = `(${minutes}:${seconds.toString().padStart(2, '0')})`;
      }
    });
  });
}

export function teardown() {}
