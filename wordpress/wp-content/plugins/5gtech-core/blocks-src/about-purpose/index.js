import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--dark' } ) }>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ a.eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ a.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				{ [ 'mission', 'vision' ].map( ( key ) => (
					<div key={ key } className="g5-editor-card">
						<RichText tagName="span" allowedFormats={ [] } value={ a[ key + 'Label' ] }
							onChange={ ( v ) => setAttributes( { [ key + 'Label' ]: v } ) } placeholder="Žyma" />
						<RichText tagName="h3" allowedFormats={ [] } value={ a[ key + 'Title' ] }
							onChange={ ( v ) => setAttributes( { [ key + 'Title' ]: v } ) } placeholder="Antraštė" />
						<RichText tagName="p" allowedFormats={ [] } value={ a[ key + 'Text' ] }
							onChange={ ( v ) => setAttributes( { [ key + 'Text' ]: v } ) } placeholder="Tekstas" />
					</div>
				) ) }
			</section>
		);
	},
	save() { return null; },
} );
