import { scrollToBlock } from './utils.js';

export function setup() {
  const ajaxBlocks = document.querySelectorAll('.ajax-posts-js');

  ajaxBlocks.forEach(block => {
    const resultsContainer = block.querySelector('.ajax-results-js');
    let currentController = null;

    if (!resultsContainer) return;

    resultsContainer.addEventListener('click', function (e) {
      const link = e.target.closest('.pagination-js a');
      if (!link) return;

      e.preventDefault();

      const url = link.getAttribute('href');

      if (currentController) {
        currentController.abort();
      }

      currentController = new AbortController();
      const signal = currentController.signal;

      block.classList.add('ajax-loading');

      fetch(url, { method: 'GET', signal })
        .then(response => {
          if (!response.ok) throw new Error('Network response was not ok');
          return response.text();
        })
        .then(html => {
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = html;
          const newGrid = tempDiv.querySelector('.ajax-results-js');

          if (newGrid) {
            resultsContainer.innerHTML = newGrid.innerHTML;
            scrollToBlock(block);
            history.pushState(null, '', url);
          }
        })
        .catch(err => {
          if (err.name !== 'AbortError') {
            console.error('Fetch error:', err);
          }
        })
        .finally(() => {
          block.classList.remove('ajax-loading');
          currentController = null;
        });
    });
  });

}

export function teardown() {}
