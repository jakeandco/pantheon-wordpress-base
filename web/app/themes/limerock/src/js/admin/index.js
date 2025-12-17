import acfFields from "./acf-fields.js";

document.addEventListener("readystatechange", function () {
  if (document.readyState === "complete" && typeof acf !== "undefined") {
    acfFields.setup();

    if (acf.addAction) {
      acf.addAction(
        "render_block_preview",
        function ($el, attributes, block) {}
      );
    }
  }
});
