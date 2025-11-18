import Dropdown from 'bootstrap/js/dist/dropdown';

/**
 * Initialize the interior subnavigation component
 */
export function setup() {
  console.log('dropdown');
  
  // Initialize collapse for the table of contents
  const header = document.querySelector('#header');

  if (!header) return;

  const originalHeaderTheme = header.getAttribute('data-bs-theme');
  const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
  const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new Dropdown(dropdownToggleEl));

  const dropdowns = document.querySelectorAll('.dropdown');

  dropdowns.forEach(dropdown => {
    dropdown.addEventListener('show.bs.dropdown', () => {
      header.classList.add('dropdown-open');
      if (window.innerWidth >= 768) {
        header.setAttribute('data-bs-theme', 'white');
      }
      // const menu = dropdown.querySelector('.dropdown-menu');
      // console.log(menu);
      // menu.classList.add('animating');
      // setTimeout(() => menu.classList.add('showing'), 10);
    });

    dropdown.addEventListener('hide.bs.dropdown', () => {
      header.classList.remove('dropdown-open');
      header.setAttribute('data-bs-theme', originalHeaderTheme);
      // const menu = dropdown.querySelector('.dropdown-menu');
      // menu.classList.remove('showing');
      // setTimeout(() => menu.classList.remove('animating'), 250);
    });
  });

  let resizeTimeout = null;

  const handleResize = () => {
    if (window.innerWidth < 768) {
      header.setAttribute('data-bs-theme', originalHeaderTheme);
    } else {
      if (header.classList.contains('dropdown-open')) {
        header.setAttribute('data-bs-theme', 'white');
      }
    }
  };

  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(handleResize, 300);
  });
}

export function teardown() {
  
} 
