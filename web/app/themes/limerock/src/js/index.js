import * as accordion from './accordion';
import * as customSelect from './customSelect';
import * as dropdown from './dropdown';
import * as fancybox from './fancybox';
import * as footnotes from './footnotes';
import * as scrollAnimation from './scrollAnimation';
import * as share from './share';
import * as swiper from './swiper';
import * as tags from './tags';
import * as video from './video';
import * as svgAnimation from './svgAnimation';
import * as archiveAjax from './archiveAjax';
import * as headerSearch from './headerSearch';
import * as headerPosition from './headerPosition';
import * as postsAjax from './postsAjax';
import * as doiLink from './doi-link';
import * as detectDevice from './detectDevice';

export function setup() {
  accordion.setup();
  customSelect.setup();
  dropdown.setup();
  fancybox.setup();
  footnotes.setup();
  scrollAnimation.setup();
  share.setup();
  swiper.setup();
  tags.setup();
  video.setup();
  svgAnimation.setup();
  archiveAjax.setup();
  headerSearch.setup();
  headerPosition.setup();
  postsAjax.setup();
  doiLink.setup();
  detectDevice.setup();
}

// necessary for storybook to use this file in its entirety
export function teardown() {
  accordion.teardown();
  customSelect.teardown();
  dropdown.teardown();
  fancybox.teardown();
  footnotes.teardown();
  scrollAnimation.teardown();
  share.teardown();
  swiper.teardown();
  tags.teardown();
  video.teardown();
  svgAnimation.teardown();
  archiveAjax.teardown();
  headerSearch.teardown();
  headerPosition.teardown();
  postsAjax.teardown();
  doiLink.teardown();
  detectDevice.teardown();
}

document.addEventListener('DOMContentLoaded', setup, false);
