import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, metric4Value, metric4Label } = attributes;
		return (
			<section { ...useBlockProps( { className: 'team-hero g5-grid-lines g5-grid-lines--dark' } ) }>
				<div className="g5-container g5-grid">
					<div className="team-hero__copy">
						<nav className="g5-breadcrumbs"><span>Pagrindinis</span><span>/</span><span>Apie mus</span></nav>
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
						<RichText tagName="h1" className="g5-display-xl" allowedFormats={ [] } value={ title }
							onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
						<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ lead }
							onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
					</div>
					<div className="team-hero__proof">
						<div className="team-hero__metric"><strong>2020</strong><span>veiklos pradžia</span></div>
						<div className="team-hero__metric"><strong>6000+</strong><span>įgyvendintų bazinių stočių</span></div>
						<div className="team-hero__metric"><strong>6</strong><span>Europos šalys</span></div>
						<div className="team-hero__metric">
							<RichText tagName="strong" allowedFormats={ [] } value={ metric4Value }
								onChange={ ( v ) => setAttributes( { metric4Value: v } ) } placeholder="Reikšmė" />
							<RichText tagName="span" allowedFormats={ [] } value={ metric4Label }
								onChange={ ( v ) => setAttributes( { metric4Label: v } ) } placeholder="Paaiškinimas" />
						</div>
					</div>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
