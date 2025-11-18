export function setup() {
  // Detect iOS and Mac devices
  const isAppleDevice = /Mac|iPhone|iPad|iPod/.test(navigator.platform)
    || (navigator.userAgent.includes("Mac") && "ontouchend" in document);

  if (isAppleDevice) {
    document.body.classList.add('apple-device');
  }
}

export function teardown() {}
