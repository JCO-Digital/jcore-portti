import { __ } from "@wordpress/i18n";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { PanelBody, FormTokenField } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { store as coreStore } from "@wordpress/core-data";
import ServerSideRender from "@wordpress/server-side-render";
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { termIds = [] } = attributes;

	// Fetch all terms from jcore-portal-slot taxonomy
	const { terms, hasResolved } = useSelect((select) => {
		const { getEntityRecords, hasFinishedResolution } = select(coreStore);
		const query = { per_page: -1 };
		return {
			terms: getEntityRecords("taxonomy", "jcore-portal-slot", query) || [],
			hasResolved: hasFinishedResolution("getEntityRecords", [
				"taxonomy",
				"jcore-portal-slot",
				query,
			]),
		};
	}, []);

	// Create a mapping of term names to IDs
	const termNameToId = {};
	const termIdToName = {};
	terms.forEach((term) => {
		termNameToId[term.name] = term.id;
		termIdToName[term.id] = term.name;
	});

	// Get selected term names for display
	const selectedTermNames = termIds
		.map((termId) => termIdToName[termId] || "")
		.filter(Boolean);

	// Get available term suggestions
	const suggestions = terms.map((term) => term.name);

	// Handle term selection changes
	const handleTermsChange = (newTermNames) => {
		const newTermIds = newTermNames
			.map((name) => termNameToId[name])
			.filter(Boolean);
		setAttributes({ termIds: newTermIds });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Settings", "jcore-portti")}>
					<FormTokenField
						label={__("Select Portal Slot Terms", "jcore-portti")}
						value={selectedTermNames}
						suggestions={suggestions}
						onChange={handleTermsChange}
						maxSuggestions={20}
						__experimentalShowHowTo={false}
						__nextHasNoMarginBottom
						help={__(
							"Select one or more portal slot terms to display a random post from those categories.",
							"jcore-portti",
						)}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				<ServerSideRender
					block="jco/portal-slot"
					attributes={{ ...attributes, preview: true }}
				/>
			</div>
		</>
	);
}
