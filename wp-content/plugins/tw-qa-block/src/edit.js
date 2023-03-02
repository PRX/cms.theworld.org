import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from "@wordpress/block-editor";
import {
	ToggleControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from "@wordpress/components";
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
	const { attributes, setAttributes } = props;
	const { question, answer, bordered } = attributes;
	const blockProps = useBlockProps();
	const classNames = blockProps.className.split(" ");
	const classNamesSet = new Set(classNames);

	if (bordered) {
		classNamesSet.add("qa-wrap--border-around");
	}

	const className = [...classNamesSet].join(" ");

	function onQuestionChange(question) {
		setAttributes({ question });
	}

	function onAnswerChange(answer) {
		setAttributes({ answer });
	}

	function onBorderedChange(bordered) {
		setAttributes({ bordered });
	}

	function resetAllOptions() {
		setAttributes({ bordered: false });
	}

	return (
		<div {...blockProps} className={className}>
			<InspectorControls key="settings">
				<ToolsPanel
					label={__("Q&A Options", "tw-qa-block")}
					resetAll={resetAllOptions}
				>
					<ToolsPanelItem
						label={__("Bordered", "tw-qa-block")}
						hasValue={() => true}
						isShownByDefault
					>
						<ToggleControl
							label={__("Bordered", "tw-qa-block")}
							help={__("Add border arround Q&A block.")}
							checked={bordered}
							onChange={onBorderedChange}
						/>
					</ToolsPanelItem>
				</ToolsPanel>
			</InspectorControls>
			<RichText
				className="qa-question"
				placeholder={__("Question...", "tw-qa-block")}
				value={question}
				onChange={onQuestionChange}
				allowedFormats={[
					"core/bold",
					"core/italic",
					"core/strikethrough",
					"core/link",
				]}
			/>
			<RichText
				className="qa-answer"
				placeholder={__("Answer...", "tw-qa-block")}
				value={answer}
				onChange={onAnswerChange}
				allowedFormats={[
					"core/bold",
					"core/italic",
					"core/strikethrough",
					"core/link",
				]}
				multiline
			/>
		</div>
	);
}
