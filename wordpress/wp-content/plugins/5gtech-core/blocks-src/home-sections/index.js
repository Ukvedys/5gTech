import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
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
	save() { return <InnerBlocks.Content />; },
} );
