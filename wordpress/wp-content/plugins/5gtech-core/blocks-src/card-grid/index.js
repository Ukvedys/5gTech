import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const innerProps = useInnerBlocksProps(
			{ className: 'g5-container card-grid g5-editor-card-grid' },
			{ allowedBlocks: [ 'g5tech/card', 'g5tech/link-card' ], template: [ [ 'g5tech/card' ] ] }
		);
		return <div { ...useBlockProps() }><div { ...innerProps } /></div>;
	},
	save() { return null; },
} );
