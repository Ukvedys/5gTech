import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead } = attributes;
		return (
			<section { ...useBlockProps( { className: 'g5-editor-hero' } ) }>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h1" className="g5-display-xl" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" className="g5-body-lg" allowedFormats={ [] } value={ lead }
					onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
				<p><em>Faktų juosta (metai, stotys, šalys) imama iš „5G TECH nustatymų“.</em></p>
			</section>
		);
	},
	save() { return null; },
} );
