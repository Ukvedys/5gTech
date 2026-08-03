import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes, clientId } ) {
		const { eyebrow, title, lead, button1Label, button2Label, metaValue3, metaLabel3 } = attributes;
		const firstSlide = useSelect(
			( select ) => {
				const children = select( 'core/block-editor' ).getBlocks( clientId );
				return children.find( ( b ) => 'g5tech/hero-slide' === b.name ) || null;
			},
			[ clientId ]
		);
		const imageId = firstSlide ? firstSlide.attributes.imageId : 0;
		const themeFile = firstSlide ? firstSlide.attributes.themeFile : '';
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const imageUrl = ( media && media.source_url ) || ( themeFile && themeUri ? themeUri + '/' + themeFile.replace( /^\//, '' ) : '' );
		const innerProps = useInnerBlocksProps( { className: 'g5-hero-slides__list' }, {
			allowedBlocks: [ 'g5tech/hero-slide' ],
			template: [ [ 'g5tech/hero-slide' ] ],
			orientation: 'horizontal',
		} );

		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Mygtukai">
						<TextControl label="1 mygtukas (į kontaktus)" value={ button1Label } onChange={ ( v ) => setAttributes( { button1Label: v } ) } />
						<TextControl label="2 mygtukas (į paslaugas)" value={ button2Label } onChange={ ( v ) => setAttributes( { button2Label: v } ) } />
					</PanelBody>
					<PanelBody title="Trečias rodiklis">
						<TextControl label="Reikšmė" value={ metaValue3 } onChange={ ( v ) => setAttributes( { metaValue3: v } ) } />
						<TextControl label="Paaiškinimas" value={ metaLabel3 } onChange={ ( v ) => setAttributes( { metaLabel3: v } ) } />
					</PanelBody>
				</InspectorControls>
				<section className="hero g5-editor-hero-canvas">
					<div className="hero-media">
						{ imageUrl ? <img className="hero-bg hero-bg--slide is-active" src={ imageUrl } alt="" /> : null }
					</div>
					<div className="hero-grid" aria-hidden="true"></div>
					<div className="container hero-inner">
						<div className="hero-copy">
							<RichText tagName="div" className="eyebrow" allowedFormats={ [] } value={ eyebrow }
								onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
							<RichText tagName="h1" allowedFormats={ [] } value={ title }
								onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
							<RichText tagName="p" className="hero-lead" allowedFormats={ [] } value={ lead }
								onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
							<div className="hero-actions">
								<span className="btn btn-primary">{ button1Label } <span className="circle">→</span></span>
								<span className="btn">{ button2Label } <span className="arrow">→</span></span>
							</div>
						</div>
						<div className="hero-meta">
							<div className="meta-item"><strong>6000+</strong><span>bazinių stočių</span></div>
							<div className="meta-item"><strong>6</strong><span>Europos šalys</span></div>
							<div className="meta-item"><strong>{ metaValue3 }</strong><span>{ metaLabel3 }</span></div>
						</div>
					</div>
				</section>
				<div className="g5-hero-slides">
					<div className="g5-hero-slides__label">Skaidrės (pirmoji rodoma viršuje):</div>
					<div { ...innerProps } />
				</div>
			</div>
		);
	},
	save() { return null; },
} );
