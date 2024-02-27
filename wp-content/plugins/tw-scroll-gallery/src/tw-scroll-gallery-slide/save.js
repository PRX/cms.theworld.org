/**
 * External dependencies
 */
import classnames from "classnames";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes }) {
	const { contentPosition, mediaAlt, mediaType, mediaUrl, mediaId } =
		attributes;

	const mediaClasses = classnames("tw-scroll-gallery-slide--media", {
		[`wp-${mediaType}-${mediaId}`]: mediaId && mediaType,
	});

	const mediaTypeRenders = {
		image: <img src={mediaUrl} alt={mediaAlt} className={mediaClasses} />,
		// video: () => <video controls src={mediaUrl} />,
	};

	const className = classnames("tw-scroll-gallery-slide", {});

	const blockProps = useBlockProps.save({
		className,
		["data-content-position"]: contentPosition || "center",
	});
	const innerBlocksProps = useInnerBlocksProps.save({
		className: "tw-scroll-gallery-slide--content",
	});

	return (
		<figure {...blockProps}>
			{mediaTypeRenders[mediaType] || null}
			<figcaption {...innerBlocksProps} />
		</figure>
	);
}
