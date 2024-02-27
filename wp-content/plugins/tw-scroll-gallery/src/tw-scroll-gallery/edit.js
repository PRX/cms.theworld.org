/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from "@wordpress/block-editor";
import {
	SelectControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit(props) {
	const { attributes, setAttributes } = props;
	const { transition } = attributes;
	const classNamesSet = new Set(["tw-scroll-gallery", "alignfull"]);
	const className = [...classNamesSet].join(" ");
	const blockProps = useBlockProps({
		className,
	});
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: ["tw/scroll-gallery-slide"],
		defaultBlock: {
			name: "tw/scroll-gallery-slide",
			attributes: {},
		},
		directInsert: true,
		renderAppender: InnerBlocks.DefaultBlockAppender,
		template: [["tw/scroll-gallery-slide", {}]],
	});
	const { children, ...finalBlockProps } = innerBlocksProps;

	finalBlockProps["data-transition"] = transition || "push";

	function onTransitionChange(newTransition) {
		setAttributes({ transition: newTransition });
	}

	function resetAllOptions() {
		setAttributes({ transition: "push" });
	}

	return (
		<div {...finalBlockProps}>
			<InspectorControls key="settings">
				<ToolsPanel
					label={__("Scroll Gallery Options", "tw-scroll-gallery")}
					resetAll={resetAllOptions}
				>
					<ToolsPanelItem
						label={__("Transition", "tw-scroll-gallery")}
						hasValue={() => true}
						isShownByDefault
					>
						<SelectControl
							label={__("Transition", "tw-scroll-gallery")}
							value={transition}
							help={
								<>
									<p>
										{__(
											"Select transition when slides change.",
											"tw-scroll-gallery",
										)}
									</p>
									<dl>
										<dt>{__("Push (Default)", "tw-scroll-gallery")}</dt>
										<dd>
											{__(
												"Next slide pushes previous slide off screen.",
												"tw-scroll-gallery",
											)}
										</dd>
										<dt>{__("Stack", "tw-scroll-gallery")}</dt>
										<dd>
											{__(
												"Next slide scrolls over top of previous slide.",
												"tw-scroll-gallery",
											)}
										</dd>
										<dt>{__("Fade", "tw-scroll-gallery")}</dt>
										<dd>
											{__("Cross fade between slides.", "tw-scroll-gallery")}
										</dd>
									</dl>
								</>
							}
							options={[
								{ value: "push", label: "Push" },
								{ value: "stack", label: "Stack" },
								{ value: "fade", label: "Fade" },
							]}
							onChange={onTransitionChange}
						/>
					</ToolsPanelItem>
				</ToolsPanel>
			</InspectorControls>
			{children}
		</div>
	);
}
