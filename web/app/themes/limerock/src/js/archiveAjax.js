import { scrollToBlock } from './utils.js';

export function setup() {
  const form = document.querySelector('.ajax-form-js');
  if (!form) return;

  const resultsContainer = form.querySelector('.ajax-results-js');
  if (!resultsContainer) return;

  const filterOptions              = form.querySelectorAll('.filter-option-js');
  const createSelectedLabelOptions = form.querySelectorAll('.create-selected-label-js');
  const resetBtns                  = form.querySelectorAll('.reset-js');
  const defaultRadios              = form.querySelectorAll('.default-radio-js');

  const selectedFiltersWrap = form.querySelector('.selected-filters-js');
  const resultsTotalWrap    = form.querySelector('.results-total-js');
  const autoSearchInput     = form.querySelector('.autosearch-on-typing-js');
  const clearSearchBtn      = form.querySelector('.clear-search-js');
  const searchInput         = form.querySelector('.search-input-js');

  let currentController = null;
  let searchTimeout     = null;

  updSelectedFiltersLabels();

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const action = form.getAttribute('action');
    const searchParams = new URLSearchParams(new FormData(form)).toString();
    const url = action + '?' + searchParams;

    ajaxLoadPosts(url);
  });

  filterOptions.forEach(option => {
    option.addEventListener('change', () => {
      handleAllFilterOption(option);
      triggerSubmit();
    });
  });

  // handle pagination ajax
  form.addEventListener('click', function (e) {
    const link = e.target.closest('.pagination-js a');
    if (!link) return;

    e.preventDefault();

    const url = link.getAttribute('href');

    ajaxLoadPosts(url);
    scrollToBlock(resultsContainer);
  });

  // remove filter
  if (selectedFiltersWrap) {
    selectedFiltersWrap.addEventListener('click', function (e) {
      const removeOption = e.target.closest('.remove-filter-js');

      if (!removeOption) return;

      const optionId = removeOption.getAttribute('data-option');
      const relatedOption = form.querySelector(`.filter-option-js[data-option="${optionId}"]`);

      if (relatedOption) {
        relatedOption.checked = false;
        setDefaultRadioOptions();
        relatedOption.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  // handle reset
  resetBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // form.reset(); // reset search

      // uncheck all filter options except the default radio
      filterOptions.forEach(input => {
        if (input.classList.contains('default-radio-js')) return;

        input.checked = false;
      });

      setDefaultRadioOptions();
      triggerSubmit();

    });
  });

  if (autoSearchInput) {
    autoSearchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        triggerSubmit();
      }, 650);
    });
  }

  if (searchInput && clearSearchBtn) {
    searchInput.addEventListener('input', () => {
      if (searchInput.value.trim() !== '') {
        clearSearchBtn.removeAttribute('disabled');
      } else {
        clearSearchBtn.setAttribute('disabled', '');
      }
    });

    clearSearchBtn.addEventListener('click', () => {
      searchInput.value = '';
      clearSearchBtn.setAttribute('disabled', '');
      triggerSubmit();
    });
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

    form.classList.add('ajax-loading');

    updSelectedFiltersLabels();

    fetch(url, { method: 'GET', signal })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
      })
      .then(html => {
        updateGridContent(html);
        history.pushState(null, '', url);
      })
      .catch(err => {
        if (err.name !== 'AbortError') {
          console.error('Fetch error:', err);
        }
      })
      .finally(() => {
        form.classList.remove('ajax-loading');
        currentController = null;
      });
  }

  function updateGridContent(response) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = response;

    const newGrid = tempDiv.querySelector('.ajax-results-js');

    updateResultsTotalInfo(newGrid);

    if (newGrid && resultsContainer) {
      resultsContainer.innerHTML = newGrid.innerHTML;
    }
  }

  function updateResultsTotalInfo(newGrid) {
    if (resultsTotalWrap && newGrid.hasAttribute('data-total')) {
      const total = newGrid.getAttribute('data-total');
      resultsTotalWrap.innerHTML = total;
      resultsContainer.setAttribute('data-total', total);
    }
  }

  function updSelectedFiltersLabels() {
    if (!selectedFiltersWrap) return;

    let selectedFiltersHTML = '';

    createSelectedLabelOptions.forEach(option => {
      const isChecked = option.checked;

      if (isChecked) {
        const optionId = option.getAttribute('data-option');
        const optionText = option.getAttribute('data-text');
        selectedFiltersHTML += createLabel(optionText, optionId);
      }
    });

    const noLabels = selectedFiltersHTML === '';

    resetBtns.forEach(btn => {
      btn.disabled = noLabels;
    });

    if (noLabels) {
      form.classList.add('hide-selected-labels')
    } else {
      form.classList.remove('hide-selected-labels')
    }

    selectedFiltersWrap.innerHTML = selectedFiltersHTML;
  }

  function createLabel(text, label) {
    return `<div class="selected-filter">
      <button
        type="button"
        class="btn btn-primary btn-sm remove-filter-js"
        aria-label="Remove filter"
        data-option="${label}"
      >${text}
        <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0.530273 7.36572L7.36564 0.530358" stroke="white" stroke-width="1.5"/>
          <path d="M7.36572 7.36572L0.530358 0.530358" stroke="white" stroke-width="1.5"/>
        </svg>
      </button>
    </div>`;
  }

  function triggerSubmit() {
    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  }

  function handleAllFilterOption(option) {
    const fieldWrap = option.closest('.labels-js');

    if (!fieldWrap) return;

    const allOption = fieldWrap.querySelector('.all-labels-js');

    if (!allOption) return;

    const optionIsChecked = option.checked;

    // clicked "All" option
    if (option.classList.contains('all-labels-js')) {
      const otherOptions = Array.from(fieldWrap.querySelectorAll('input[type="checkbox"]'))
        .filter(input => input !== allOption);

      otherOptions.forEach(input => input.checked = optionIsChecked);
      return;
    }

    // clicked not "All" option
    const otherOptions = Array.from(fieldWrap.querySelectorAll('input[type="checkbox"]'))
      .filter(input => !input.classList.contains('all-labels-js'));

    const allChecked = otherOptions.every(input => input.checked);
    allOption.checked = allChecked;
  }

  function setDefaultRadioOptions() {

    const radioGroups = {};

    form.querySelectorAll('input[type="radio"]').forEach(input => {
      const name = input.getAttribute('name');
      if (!radioGroups[name]) {
        radioGroups[name] = [];
      }
      radioGroups[name].push(input);
    });

    Object.values(radioGroups).forEach(group => {
      const anyChecked = group.some(radio => radio.checked);

      // if none checked, select default radio
      if (!anyChecked) {
        const defaultOption = group.find(radio =>
          radio.classList.contains('default-radio-js')
        );
        if (defaultOption) {
          defaultOption.checked = true;
        }
      }
    });
  }

}

export function teardown() {}
