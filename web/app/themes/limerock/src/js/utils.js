export function scrollToBlock(blockToScrollTo) {
  if (!blockToScrollTo) return;

  const header = document.querySelector('.fixed-top');
  const headerHeight = header ? header.offsetHeight : 0;

  const currentY = window.scrollY;
  const targetY = blockToScrollTo.getBoundingClientRect().top + currentY;

  const isScrollingDown = targetY > currentY;

  const finalOffset = targetY - (isScrollingDown ? 0 : headerHeight);

  window.scrollTo({
    top: finalOffset,
    behavior: 'smooth'
  });
}
