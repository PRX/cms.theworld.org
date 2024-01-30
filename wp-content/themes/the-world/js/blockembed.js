wp.domReady(function () {
  //Disable Gutenberg Tour Guide popup.
  wp.data.select("core/edit-post").isFeatureActive("welcomeGuide") &&
    wp.data.dispatch("core/edit-post").toggleFeature("welcomeGuide");

  // Make Featured Image opened initially.
  if (!wp.data.select("core/edit-post").isEditorPanelOpened("featured-image")) {
    wp.data
      .dispatch("core/edit-post")
      .toggleEditorPanelOpened("featured-image");
  }

  // Allow only certain embed variants.
  const allowedEmbedBlocks = ["twitter", "youtube", "vimeo"];

  wp.blocks.getBlockVariations("core/embed").forEach(function (blockVariation) {
    if (-1 === allowedEmbedBlocks.indexOf(blockVariation.name)) {
      wp.blocks.unregisterBlockVariation("core/embed", blockVariation.name);
    }
  });

  // Remove image style options.
  wp.blocks.unregisterBlockStyle("core/image", ["default", "rounded"]);

  // Remove text format options.
  wp.richText.unregisterFormatType("core/footnote");
  wp.richText.unregisterFormatType("core/text-color");
  wp.richText.unregisterFormatType("core/image");
});

// Adjust block settings not configurable in `theme.json`.
wp.hooks.addFilter(
  "blocks.registerBlockType",
  "the-world/the-world/log-block-settings",
  (settings, name) => {
    switch (name) {
      case "core/audio":
        // Disable supports for audio block.
        return {
          ...settings,
          supports: {
            align: false,
            anchor: false,
            spacing: {},
          },
        };

      default:
        return settings;
    }
  },
  100
);
