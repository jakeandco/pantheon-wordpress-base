export function scrollToBlock(blockToScrollTo) {
  if (blockToScrollTo) {
    const header = document.querySelector('.fixed-top');
    const headerHeight = header ? header.offsetHeight : 0;
    const offsetTop = blockToScrollTo.getBoundingClientRect().top + window.scrollY - headerHeight;

    window.scrollTo({
      top: offsetTop,
      behavior: 'smooth'
    });
  }
}
