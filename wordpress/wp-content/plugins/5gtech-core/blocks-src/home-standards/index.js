import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender block={ metadata.name } />
				</Disabled>
			</div>
		);
	},
	save() { return null; },
} );
