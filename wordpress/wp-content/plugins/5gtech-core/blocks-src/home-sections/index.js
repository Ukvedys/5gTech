import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, {} );
		return (
			<div { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<div { ...innerProps } />
			</div>
		);
	},
	save() { return null; },
} );
