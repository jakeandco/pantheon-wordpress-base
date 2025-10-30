import SimpleBar from 'simplebar';
import 'simplebar-core/dist/simplebar.css';

import ResizeObserver from 'resize-observer-polyfill';
window.ResizeObserver = ResizeObserver;

export function setup() {
  const toggleSearchBtn  = document.querySelectorAll('.toggle-search-js');
  const closeSearchBtn   = document.querySelectorAll('.close-search-js');

  const searchInput      = document.querySelector('.header-search-input-js');
  const resultsContainer = document.querySelector('.header-search-result-js');
  const clearSearchBtn   = document.querySelector('.clear-header-search-js');
  const form             = document.querySelector('.header-search-form-js');
  const searchModal      = document.querySelector('.search-modal-js');
  const toSearchPage     = document.querySelector('.to-search-page-js');
  const header           = document.querySelector('#header');

  const html = document.documentElement;
  const body = document.body;

  let currentController = null;
  let searchTimeout     = null;

  if (!form || !searchModal || !searchInput) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const url = getCurrentSearchUrl();

    ajaxLoadPosts(url);
  });

  toggleSearchBtn.forEach(btn => {
    btn.addEventListener('click', () => {
      if (html.classList.contains('show-header-search')) {
        closeSearch();
      } else {
        openSearch();
      }
    });
  });

  closeSearchBtn.forEach(btn => {
    btn.addEventListener('click', closeSearch);
  });

  searchInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    const hasSearchText = isNotEmptySearch();

    if (hasSearchText) {
      searchTimeout = setTimeout(() => {
        triggerSubmit();
      }, 650);
    } else {
      updSearchState();
    }
  });

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
       clearSearch();
    });
  }

  if (toSearchPage) {
    toSearchPage.addEventListener('click', () => {
      window.location.href = getCurrentSearchUrl();
    });
  }

  // handle pagination ajax
  searchModal.addEventListener('click', function (e) {
    const link = e.target.closest('.pagination-js a');

    if (!link) return;

    e.preventDefault();

    const url = link.getAttribute('href');

    ajaxLoadPosts(url);
  });

  window.addEventListener('pageshow', () => {
    clearSearch();
  });

  function openSearch() {
    const hadScrollbar = window.innerWidth > document.documentElement.clientWidth;

    if (!html.classList.contains('show-header-search')) {
      if (hadScrollbar) {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        body.style.paddingRight = `${scrollbarWidth}px`;
        if (header) header.style.paddingRight = `${scrollbarWidth}px`;
      }

      html.classList.add('show-header-search');
    }
  }

  function closeSearch() {
    if (html.classList.contains('show-header-search')) {
      html.classList.remove('show-header-search');
      body.style.paddingRight = '';
      if (header) header.style.paddingRight = '';
    }
  }

  function ajaxLoadPosts(url) {
    if (currentController) {
      currentController.abort();
    }

    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    currentController = new AbortController();
    const signal = currentController.signal;

    searchModal.classList.add('ajax-loading');

    fetch(url, { method: 'GET', signal })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
      })
      .then(html => {
        updateGridContent(html);
      })
      .catch(err => {
        if (err.name !== 'AbortError') {
          console.error('Fetch error:', err);
        }
      })
      .finally(() => {
        updSearchState();
        searchModal.classList.remove('ajax-loading');
        currentController = null;
        scrollToTop();
      });
  }

  function updateGridContent(response) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = response;

    const newGrid = tempDiv.querySelector('.ajax-results-js');
    if (newGrid && resultsContainer) {
      resultsContainer.innerHTML = newGrid.innerHTML;
    }
  }

  function updSearchState() {
    const hasSearchText = isNotEmptySearch();
    const state = hasSearchText ? 'not-empty' : 'empty';

    searchModal.setAttribute('data-state', state);
  }

  function triggerSubmit() {
    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  }

  function isNotEmptySearch() {
    const value = searchInput.value.trim();
    return value.length > 0;
  }

  function getCurrentSearchUrl() {
    const action = form.getAttribute('action');
    const searchParams = new URLSearchParams(new FormData(form)).toString();
    const url = action + '/?' + searchParams;

    return url;
  }

  function clearSearch() {
    searchInput.value = '';
    updSearchState();
  }

  function scrollToTop() {
    const el = document.querySelector('.search-info-wrap');
    const simplebar = SimpleBar.instances.get(el);
    if (simplebar) {
      simplebar.getScrollElement().scrollTop = 0;
      // smooth scroll if needed
      // simplebar.getScrollElement().scrollTo({
      //   top: 0,
      //   behavior: 'smooth',
      // });
    }
  }
}

export function teardown() {}
