import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const innerProps = useInnerBlocksProps(
			{ className: 'g5-container steps g5-editor-steps' },
			{ allowedBlocks: [ 'g5tech/step' ], template: [ [ 'g5tech/step' ] ] }
		);
		return <div { ...useBlockProps() }><ol { ...innerProps } /></div>;
	},
	save() { return <InnerBlocks.Content />; },
} );
