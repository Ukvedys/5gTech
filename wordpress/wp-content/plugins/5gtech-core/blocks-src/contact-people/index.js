import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Tiesioginiai kontaktai</strong></p>
				<p><em>Rodomi komandos nariai, kuriems skiltyje „Komanda“ pažymėta „Rodyti kontaktuose“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
