import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, useDispatch } from '@wordpress/data';
import {
	DateTimePicker,
	TextControl,
	SelectControl,
	PanelRow,
} from '@wordpress/components';

const CampaignContentSettings = () => {
	const postType = useSelect( ( select ) =>
		select( 'core/editor' ).getCurrentPostType()
	);

	const meta = useSelect( ( select ) =>
		select( 'core/editor' ).getEditedPostAttribute( 'meta' )
	);

	const { editPost } = useDispatch( 'core/editor' );

	if ( postType !== 'jcore-portal-content' ) {
		return null;
	}

	const updateMeta = ( key, value ) => {
		editPost( { meta: { ...meta, [ key ]: value } } );
	};

	return (
		<PluginDocumentSettingPanel
			name="jcore-portti-settings"
			title={ __( 'Campaign Settings', 'jcore-portti' ) }
		>
			<div className="jcore-portti-meta-fields">
				<PanelRow>
					<div style={ { marginBottom: '20px', width: '100%' } }>
						<label
							style={ {
								display: 'block',
								marginBottom: '8px',
								fontWeight: '600',
							} }
						>
							{ __( 'Start Date', 'jcore-portti' ) }
						</label>
						<DateTimePicker
							currentDate={ meta._jcore_portti_start_date }
							onChange={ ( date ) =>
								updateMeta( '_jcore_portti_start_date', date )
							}
							is12Hour={ false }
						/>
					</div>
				</PanelRow>

				<PanelRow>
					<div style={ { marginBottom: '20px', width: '100%' } }>
						<label
							style={ {
								display: 'block',
								marginBottom: '8px',
								fontWeight: '600',
							} }
						>
							{ __( 'End Date', 'jcore-portti' ) }
						</label>
						<DateTimePicker
							currentDate={ meta._jcore_portti_end_date }
							onChange={ ( date ) =>
								updateMeta( '_jcore_portti_end_date', date )
							}
							is12Hour={ false }
						/>
					</div>
				</PanelRow>

				<TextControl
					label={ __( 'Route Path', 'jcore-portti' ) }
					value={ meta._jcore_portti_route_path }
					onChange={ ( value ) =>
						updateMeta( '_jcore_portti_route_path', value )
					}
					help={ __(
						'Example: /products/* or /shop. Leave empty for all pages.',
						'jcore-portti'
					) }
				/>

				<SelectControl
					label={ __( 'Priority', 'jcore-portti' ) }
					value={ meta._jcore_portti_priority }
					options={ [
						{ label: __( 'High', 'jcore-portti' ), value: 'high' },
						{
							label: __( 'Medium', 'jcore-portti' ),
							value: 'medium',
						},
						{ label: __( 'Low', 'jcore-portti' ), value: 'low' },
					] }
					onChange={ ( value ) =>
						updateMeta( '_jcore_portti_priority', value )
					}
				/>
			</div>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'jcore-portti-sidebar', {
	render: CampaignContentSettings,
} );
