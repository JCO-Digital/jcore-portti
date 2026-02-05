import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ExternalLink,
	Placeholder,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   props               The edit props.
 * @param {Object}   props.attributes    The block attributes.
 * @param {Function} props.setAttributes The function to set attributes.
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { slotId, previewPostId } = attributes;

	// Fetch all terms from jcore-portal-slot taxonomy
	const { terms, hasResolved } = useSelect( ( select ) => {
		const { getEntityRecords, hasFinishedResolution } = select( coreStore );
		const query = { per_page: -1 };
		return {
			terms:
				getEntityRecords( 'taxonomy', 'jcore-portal-slot', query ) ||
				[],
			hasResolved: hasFinishedResolution( 'getEntityRecords', [
				'taxonomy',
				'jcore-portal-slot',
				query,
			] ),
		};
	}, [] );

	// Fetch available campaign content for the selected slot
	const { campaignPosts } = useSelect(
		( select ) => {
			if ( ! slotId || ! terms.length ) return { campaignPosts: [] };
			const term = terms.find( ( t ) => t.slug === slotId );
			if ( ! term ) return { campaignPosts: [] };

			const { getEntityRecords } = select( coreStore );
			const query = {
				per_page: -1,
				'jcore-portal-slot': term.id,
				status: 'publish',
			};
			return {
				campaignPosts:
					getEntityRecords(
						'postType',
						'jcore-portal-content',
						query
					) || [],
			};
		},
		[ slotId, terms ]
	);

	const slotOptions = [
		{ label: __( 'Select a slot...', 'jcore-portti' ), value: '' },
		...terms.map( ( term ) => ( {
			label: term.name,
			value: term.slug,
		} ) ),
	];

	const previewOptions = [
		{ label: __( 'Automatic (Based on rules)', 'jcore-portti' ), value: 0 },
		...campaignPosts.map( ( post ) => ( {
			label: post.title.rendered || `#${ post.id }`,
			value: post.id,
		} ) ),
	];

	const manageSlotsUrl =
		'/wp-admin/edit-tags.php?taxonomy=jcore-portal-slot&post_type=jcore-portal-content';

	const inspectorControls = (
		<InspectorControls>
			<PanelBody title={ __( 'Settings', 'jcore-portti' ) }>
				<SelectControl
					label={ __( 'Portal Slot', 'jcore-portti' ) }
					value={ slotId }
					options={ slotOptions }
					onChange={ ( value ) => setAttributes( { slotId: value } ) }
					help={ __(
						'Choose the slot location for this block.',
						'jcore-portti'
					) }
				/>
				{ slotId && (
					<SelectControl
						label={ __( 'Preview Content', 'jcore-portti' ) }
						value={ previewPostId }
						options={ previewOptions }
						onChange={ ( value ) =>
							setAttributes( {
								previewPostId: parseInt( value, 10 ),
							} )
						}
						help={ __(
							'Force a specific campaign to show in the editor preview.',
							'jcore-portti'
						) }
					/>
				) }
				<ExternalLink href={ manageSlotsUrl }>
					{ __( 'Manage Portal Slots', 'jcore-portti' ) }
				</ExternalLink>
			</PanelBody>
		</InspectorControls>
	);

	if ( ! slotId ) {
		return (
			<div { ...useBlockProps() }>
				{ inspectorControls }
				<Placeholder
					icon="share-alt"
					label={ __( 'Portal Slot', 'jcore-portti' ) }
					instructions={ __(
						'Select a portal slot in the sidebar to display campaign content.',
						'jcore-portti'
					) }
				/>
			</div>
		);
	}

	return (
		<div { ...useBlockProps() }>
			{ inspectorControls }
			<ServerSideRender
				block="jco/portal-slot"
				attributes={ attributes }
			/>
			<div className="jcore-portal-slot-fallback-editor">
				<hr />
				<p
					style={ {
						fontSize: '11px',
						opacity: 0.7,
						marginBottom: '10px',
					} }
				>
					{ __(
						'Fallback Content (Shown if no active campaign matches):',
						'jcore-portti'
					) }
				</p>
				<InnerBlocks />
			</div>
		</div>
	);
}
