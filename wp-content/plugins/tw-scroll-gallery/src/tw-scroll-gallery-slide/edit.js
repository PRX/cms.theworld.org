/**
 * External dependencies
 */
import classnames from "classnames";

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __, _x } from "@wordpress/i18n";

import { useSelect } from "@wordpress/data";
import { useState, useRef } from "@wordpress/element";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	BlockControls,
	BlockIcon,
	BlockVerticalAlignmentControl,
	InspectorControls,
	MediaPlaceholder,
	MediaReplaceFlow,
	useInnerBlocksProps,
	useBlockProps,
	__experimentalImageURLInputUI as ImageURLInputUI,
	__experimentalImageSizeControl as ImageSizeControl,
	store as blockEditorStore,
	useBlockEditingMode,
} from "@wordpress/block-editor";
import {
	PanelBody,
	RangeControl,
	TextareaControl,
	ToggleControl,
	ToolbarButton,
	ExternalLink,
	FocalPointPicker,
} from "@wordpress/components";
import { isBlobURL, getBlobTypeByURL } from "@wordpress/blob";
import {
	image as MediaIcon,
	justifyCenter as JustifyCenterIcon,
	justifyLeft as JustifyLeftIcon,
	justifyRight as JustifyRightIcon,
} from "@wordpress/icons";
import { useDispatch } from "@wordpress/data";
import { store as coreStore } from "@wordpress/core-data";
import { store as noticesStore } from "@wordpress/notices";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

function attributesFromMedia({
	attributes: { linkDestination, href },
	setAttributes,
}) {
	return (media) => {
		if (!media || !media.url) {
			setAttributes({
				mediaAlt: undefined,
				mediaId: undefined,
				mediaType: undefined,
				mediaUrl: undefined,
			});
			return;
		}

		if (isBlobURL(media.url)) {
			media.type = getBlobTypeByURL(media.url);
		}

		let mediaType;
		let src;
		// For media selections originated from a file upload.
		if (media.media_type) {
			if (media.media_type === "image") {
				mediaType = "image";
			} else {
				// only images and videos are accepted so if the media_type is not an image we can assume it is a video.
				// video contain the media type of 'file' in the object returned from the rest api.
				mediaType = "video";
			}
		} else {
			// For media selections originated from existing files in the media library.
			mediaType = media.type;
		}

		if (mediaType === "image") {
			// Try the "large" size URL, falling back to the "full" size URL below.
			src =
				media.sizes?.large?.url ||
				// eslint-disable-next-line camelcase
				media.media_details?.sizes?.large?.source_url;
		}

		setAttributes({
			mediaAlt: media.alt,
			mediaId: media.id,
			mediaType,
			mediaUrl: src || media.url,
		});
	};
}

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, isSelected, setAttributes }) {
	const { contentPosition, mediaAlt, mediaId, mediaType, mediaUrl } =
		attributes;

	const image = useSelect(
		(select) => {
			return mediaId
				? select(coreStore).getMedia(mediaId, {
						context: "view",
				  })
				: null;
		},
		[mediaId],
	);

	const classNames = classnames({
		"is-selected": isSelected,
	});

	const onSelectMedia = attributesFromMedia({ attributes, setAttributes });

	const { createErrorNotice } = useDispatch(noticesStore);

	const onUploadError = (message) => {
		createErrorNotice(message, { type: "snackbar" });
	};

	function onMediaAltChange(newMediaAlt) {
		setAttributes({ mediaAlt: newMediaAlt });
	}

	const mediaTextGeneralSettings = (
		<PanelBody title={__("Settings")}>
			{mediaType === "image" && (
				<TextareaControl
					__nextHasNoMarginBottom
					label={__("Alternative text")}
					value={mediaAlt}
					onChange={onMediaAltChange}
					help={
						<>
							<ExternalLink href="https://www.w3.org/WAI/tutorials/images/decision-tree">
								{__("Describe the purpose of the image.")}
							</ExternalLink>
							<br />
							{__("Leave empty if decorative.")}
						</>
					}
				/>
			)}
		</PanelBody>
	);

	const mediaClasses = classnames("tw-scroll-gallery-slide--media", {
		[`wp-${mediaType}-${mediaId}`]: mediaId && mediaType,
	});

	const mediaTypeRenders = {
		image: <img src={mediaUrl} alt={mediaAlt} className={mediaClasses} />,
		// video: () => <video controls src={mediaUrl} />,
	};

	const blockProps = useBlockProps({
		className: classNames,
		["data-content-position"]: contentPosition || "center",
	});

	const innerBlocksProps = useInnerBlocksProps(
		{ className: "tw-scroll-gallery-slide--content" },
		{
			template: [
				[
					"core/paragraph",
					{
						placeholder: _x("Content...", "tw-scroll-gallery-slide"),
					},
				],
			],
			allowedBlocks: ["core/paragraph"],
		},
	);

	const blockEditingMode = useBlockEditingMode();

	return (
		<>
			<InspectorControls>{mediaTextGeneralSettings}</InspectorControls>
			<BlockControls group="block">
				{blockEditingMode === "default" && (
					<>
						<ToolbarButton
							icon={JustifyLeftIcon}
							title={__("Show content on left")}
							isActive={contentPosition === "left"}
							onClick={() => setAttributes({ contentPosition: "left" })}
						/>
						<ToolbarButton
							icon={JustifyCenterIcon}
							title={__("Center content")}
							isActive={contentPosition === "center"}
							onClick={() => setAttributes({ contentPosition: "center" })}
						/>
						<ToolbarButton
							icon={JustifyRightIcon}
							title={__("Show content on right")}
							isActive={contentPosition === "right"}
							onClick={() => setAttributes({ contentPosition: "right" })}
						/>
					</>
				)}

				{mediaUrl && (
					<MediaReplaceFlow
						mediaId={mediaId}
						mediaURL={mediaUrl}
						allowedTypes={["image"]}
						accept="image/*"
						onSelect={onSelectMedia}
					/>
				)}
			</BlockControls>
			<figure {...blockProps}>
				{mediaTypeRenders[mediaType] || null}
				<MediaPlaceholder
					icon={<BlockIcon icon={MediaIcon} />}
					labels={{
						title: __("Image area"),
					}}
					className="tw-scroll-gallery-slide--media-placholder"
					onSelect={onSelectMedia}
					accept="image/*"
					allowedTypes={["image"]}
					onError={onUploadError}
					disableMediaButtons={mediaUrl}
				/>
				<figcaption {...innerBlocksProps} />
			</figure>
		</>
	);
}
