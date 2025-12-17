export function setup() {
  if (navigator.userAgentData) {
    // Newer browsers
    if (navigator.userAgentData.platform === "macOS") {
      document.documentElement.classList.add('mac');
    }
  } else {
    // Fallback
    if (navigator.platform.toUpperCase().indexOf('MAC') >= 0) {
      document.documentElement.classList.add('mac');
    }
  }
}

export function teardown() {}
