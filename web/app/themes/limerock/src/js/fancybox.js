import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { Fancybox } from "@fancyapps/ui";

export function setup() {
  console.log('fancybox');

  Fancybox.bind("[data-fancybox]");
}

export function teardown() {

}
