import { useBlockProps, RichText } from "@wordpress/block-editor";

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save(props) {
	const { attributes } = props;
	const { question, answer, bordered } = attributes;
	const blockProps = useBlockProps.save();
	const classNames = blockProps.className?.split(" ");
	const classNamesSet = new Set(classNames);

	classNamesSet.delete("wp-block-tw-qa-block");
	classNamesSet.add("qa-wrap");

	if (bordered) {
		classNamesSet.add("qa-wrap--border-around");
	}

	const className = [...classNamesSet].join(" ");

	return (
		<div {...useBlockProps.save()} className={className}>
			<RichText.Content
				tagName="div"
				className="qa-question"
				value={question}
			/>
			<RichText.Content tagName="div" className="qa-answer" value={answer} />
		</div>
	);
}
