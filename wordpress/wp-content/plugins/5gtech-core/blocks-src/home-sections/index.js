import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const innerProps = useInnerBlocksProps( { className: 'home-sections' }, {} );
		return (
			<div { ...useBlockProps() }>
				<div { ...innerProps } />
			</div>
		);
	},
	save() { return null; },
} );
