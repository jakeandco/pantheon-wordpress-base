export function setup() {
  const SELECTOR = '.fixed-top';
  const WRAP_CLASS = 'fixed-top-wrap';
  let resizeObserver = null;
  let mutationObserver = null;
  let resizeTimer = null;

  function updateHeight(fixedTopEl) {
    if (!fixedTopEl) return;
    const wrap = fixedTopEl.parentElement;
    if (!wrap || !wrap.classList.contains(WRAP_CLASS)) return;
    const h = Math.round(fixedTopEl.getBoundingClientRect().height);
    wrap.style.height = h + 'px';
    wrap.style.width = '100%';
  }

  function ensureWrapped() {
    const fixedTopEl = document.querySelector(SELECTOR);
    if (!fixedTopEl) return null;

    if (fixedTopEl.parentElement && fixedTopEl.parentElement.classList.contains(WRAP_CLASS)) {
      updateHeight(fixedTopEl);
      return fixedTopEl;
    }

    const wrap = document.createElement('div');
    wrap.className = WRAP_CLASS;
    fixedTopEl.parentNode.insertBefore(wrap, fixedTopEl);
    wrap.appendChild(fixedTopEl);

    wrap.style.display = 'block';
    wrap.style.width = '100%';
    updateHeight(fixedTopEl);

    return fixedTopEl;
  }

  function debounceUpdate(fixedTopEl, delay = 100) {
    if (resizeTimer) clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => updateHeight(fixedTopEl), delay);
  }

  function attachObservers(fixedTopEl) {
    if (!fixedTopEl) return;

    if (window.ResizeObserver) {
      resizeObserver = new ResizeObserver(() => updateHeight(fixedTopEl));
      try { resizeObserver.observe(fixedTopEl); } catch (e) { /* ignore */ }
    }

    if (window.MutationObserver) {
      mutationObserver = new MutationObserver(() => debounceUpdate(fixedTopEl));
      mutationObserver.observe(fixedTopEl, { childList: true, subtree: true, characterData: true });
    }

    window.addEventListener('resize', () => debounceUpdate(fixedTopEl));
  }

  function initFixedTopWrapper() {
    const el = ensureWrapped();
    if (!el) return;
    attachObservers(el);
    window.addEventListener('load', () => updateHeight(el));
    requestAnimationFrame(() => updateHeight(el));
    window.updateFixedTopWrap = function () { updateHeight(el); };
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFixedTopWrapper);
  } else {
    initFixedTopWrapper();
  }
}

export function teardown() {}
