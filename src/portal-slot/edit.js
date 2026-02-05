import { __ } from "@wordpress/i18n";
import {
	InspectorControls,
	useBlockProps,
	InnerBlocks,
	InspectorAdvancedControls,
} from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	ExternalLink,
	Placeholder,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { store as coreStore } from "@wordpress/core-data";
import ServerSideRender from "@wordpress/server-side-render";
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   props               The edit props.
 * @param {Object}   props.attributes    The block attributes.
 * @param {Function} props.setAttributes The function to set attributes.
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { slotId, maxItems, rotate } = attributes;

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

	const slotOptions = [
		{ label: __("Select a slot...", "jcore-portti"), value: "" },
		...terms.map((term) => ({
			label: term.name,
			value: term.slug,
		})),
	];

	const manageSlotsUrl =
		"/wp-admin/edit-tags.php?taxonomy=jcore-portal-slot&post_type=jcore-portal-content";

	const inspectorControls = (
		<>
			<InspectorControls>
				<PanelBody title={__("Settings", "jcore-portti")}>
					<SelectControl
						label={__("Portal Slot", "jcore-portti")}
						value={slotId}
						options={slotOptions}
						onChange={(value) => setAttributes({ slotId: value })}
						help={__(
							"Choose the slot location for this block.",
							"jcore-portti",
						)}
					/>
					<ExternalLink href={manageSlotsUrl}>
						{__("Manage Portal Slots", "jcore-portti")}
					</ExternalLink>
				</PanelBody>
			</InspectorControls>
			<InspectorAdvancedControls>
				<NumberControl
					label={__("Max number of items", "jcore-portti")}
					value={maxItems}
					onChange={(value) =>
						setAttributes({
							maxItems: parseInt(value, 10) || 1,
						})
					}
					min={1}
					help={__(
						"Maximum number of campaign items to display.",
						"jcore-portti",
					)}
				/>
				<ToggleControl
					label={__("Rotate items", "jcore-portti")}
					checked={rotate}
					onChange={(value) => setAttributes({ rotate: value })}
					help={__(
						"When enabled, items will rotate back to the start when running out of items in the stack.",
						"jcore-portti",
					)}
				/>
			</InspectorAdvancedControls>
		</>
	);

	if (!slotId) {
		return (
			<div {...useBlockProps()}>
				{inspectorControls}
				<Placeholder
					icon="share-alt"
					label={__("Portal Slot", "jcore-portti")}
					instructions={__(
						"Select a portal slot in the sidebar to display campaign content.",
						"jcore-portti",
					)}
				/>
			</div>
		);
	}

	return (
		<div {...useBlockProps()}>
			{inspectorControls}
			<ServerSideRender block="jco/portal-slot" attributes={attributes} />
			<div className="jcore-portal-slot-fallback-editor">
				<hr />
				<p
					style={{
						fontSize: "11px",
						opacity: 0.7,
						marginBottom: "10px",
					}}
				>
					{__(
						"Fallback Content (Shown if no active campaign matches):",
						"jcore-portti",
					)}
				</p>
				<InnerBlocks />
			</div>
		</div>
	);
}
