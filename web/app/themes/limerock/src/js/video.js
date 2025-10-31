export function setup() {
  let hoverVideos = document.querySelectorAll('[data-hover-play]');

   hoverVideos.forEach(video => {
    const block = video.closest('.block_single-media-item');
    const durationSpan = block?.querySelector('.video-duration');
    block.addEventListener('mouseenter', () => {
      video.play();
    });

    block.addEventListener('mouseleave', () => {
      video.pause();
      video.currentTime = 0;
    });

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
