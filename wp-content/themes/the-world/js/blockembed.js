wp.domReady(function () {
  const allowedEmbedBlocks = ["twitter", "youtube", "vimeo"];

  wp.blocks.getBlockVariations("core/embed").forEach(function (blockVariation) {
    if (-1 === allowedEmbedBlocks.indexOf(blockVariation.name)) {
      wp.blocks.unregisterBlockVariation("core/embed", blockVariation.name);
    }
  });

  wp.blocks.unregisterBlockStyle("core/image", ["default", "rounded"]);
});
