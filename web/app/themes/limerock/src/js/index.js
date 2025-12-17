import * as customSelect from "./customSelect";
import * as footnotes from "./footnotes";
import * as scrollAnimation from "./scrollAnimation";
import * as share from "./share";
import * as tags from "./tags";
import * as video from "./video";
import * as doiLink from "./doi-link";
import * as detectDevice from "./detectDevice";

export function setup() {
  customSelect.setup();
  footnotes.setup();
  scrollAnimation.setup();
  share.setup();
  tags.setup();
  video.setup();
  doiLink.setup();
  detectDevice.setup();
  jumpLinks.setup();
}

// necessary for storybook to use this file in its entirety
export function teardown() {
  customSelect.teardown();
  footnotes.teardown();
  scrollAnimation.teardown();
  share.teardown();
  tags.teardown();
  video.teardown();
  doiLink.teardown();
  detectDevice.teardown();
  jumpLinks.teardown();
}

document.addEventListener("DOMContentLoaded", setup, false);
