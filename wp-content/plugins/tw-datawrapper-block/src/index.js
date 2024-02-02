/**
 * Registers a new block variation provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-blocks/#registerBlockVariation
 */
import { registerBlockVariation } from "@wordpress/blocks";
import domReady from "@wordpress/dom-ready";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * Component to render SVG of DataWrapper logo icon.
 * @returns JSX.Element
 */
const embedDataWrapperIcon = () => (
	<svg
		version="1.0"
		xmlns="http://www.w3.org/2000/svg"
		width="520.000000pt"
		height="520.000000pt"
		viewBox="0 0 520.000000 520.000000"
		preserveAspectRatio="xMidYMid meet"
	>
		<g
			transform="translate(0.000000,520.000000) scale(0.100000,-0.100000)"
			fill="#000000"
			stroke="none"
		>
			<path
				d="M1040 2595 l0 -1565 1565 0 1566 0 -3 113 -3 112 -1447 3 -1448 2 -2
1448 -3 1447 -112 3 -113 3 0 -1566z"
			/>
			<path
				d="M2160 2835 l0 -877 323 4 c365 5 410 12 562 88 198 98 324 257 387
490 21 77 23 106 22 300 0 251 -12 312 -91 470 -39 77 -63 111 -133 181 -71
73 -100 94 -180 133 -160 78 -170 79 -552 84 l-338 3 0 -876z m597 625 c195
-26 317 -143 375 -359 29 -107 31 -389 4 -506 -45 -196 -145 -316 -307 -366
-49 -15 -92 -19 -215 -19 l-154 0 0 630 0 630 109 0 c60 0 145 -5 188 -10z"
			/>
		</g>
	</svg>
);

/*
 * New `core/group` block variation.
 */
const variation = {
	name: "datawrapper",
	title: "DataWrapper",
	icon: embedDataWrapperIcon,
	keywords: [__("graph"), __("chart")],
	description: __("Embed a DataWrapper chart."),
	patterns: [/^https?:\/\/datawrapper\.dwcdn\.net\/.+/i],
	attributes: { providerNameSlug: "datawrapper", responsive: true },
	isActive: (blockAttributes, variationAttributes) =>
		blockAttributes.providerNameSlug === variationAttributes.providerNameSlug,
};

registerBlockVariation("core/embed", variation);

/**
 * Add message event handlers that will update iframe height when iframe is resized.
 */
domReady(() => {
	window.addEventListener("message", function (event) {
		if (typeof event.data["datawrapper-height"] !== "undefined") {
			var iframes = document.querySelectorAll("iframe");
			for (var chartId in event.data["datawrapper-height"]) {
				for (var i = 0; i < iframes.length; i++) {
					if (iframes[i].contentWindow === event.source) {
						var iframe = iframes[i];
						iframe.style.height =
							event.data["datawrapper-height"][chartId] + "px";
					}
				}
			}
		}
	});
});
