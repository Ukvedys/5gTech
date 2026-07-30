<?php
/**
 * Build-time helper for preparing a first translation catalogue draft.
 *
 * Usage:
 * php tools/build-translation-catalog.php source.txt en output.php
 *
 * Generated copy must be reviewed before it is used publicly. The public
 * website never calls an external translation service at runtime.
 */

if ( 4 !== $argc ) {
	fwrite( STDERR, "Usage: php build-translation-catalog.php source.txt en|de output.php\n" );
	exit( 1 );
}

[ , $source_file, $target_language, $output_file ] = $argv;

if ( ! in_array( $target_language, array( 'en', 'de' ), true ) || ! is_file( $source_file ) ) {
	fwrite( STDERR, "Invalid source file or target language.\n" );
	exit( 1 );
}

$skip = array_flip(
	array(
		'1&1',
		'3Z-RFVision',
		'450connect',
		'5G TECH',
		'5GTECH',
		'5GTECH Academy',
		'Aleksandras Iljinas',
		'Bitė',
		'CommScope',
		'Dantherm',
		'Delta',
		'Deutsche Telekom',
		'Eimantas Žemaitis',
		'Elisa',
		'Eltek',
		'Enersys',
		'Ericsson',
		'FIAMM',
		'Huawei',
		'Kristina Naginevičienė',
		'LPR',
		'Nerijus Bazinas',
		'Nokia',
		'ODF',
		'OTDR',
		'SiteMaster',
		'Sonel',
		'TDC',
		'Tadas Grabauskas',
		'Tele2',
		'Telefónica / O2',
		'Telenor',
		'Telia',
		'UAB „5GTECH“',
		'Vodafone',
		'ZTE',
	)
);

$lines = file( $source_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
$lines = array_values(
	array_unique(
		array_filter(
			array_map( 'trim', $lines ),
			static function ( $line ) use ( $skip ) {
				if ( '' === $line || str_starts_with( $line, '### ' ) || str_starts_with( $line, '/' ) || isset( $skip[ $line ] ) ) {
					return false;
				}

				if ( preg_match( '/^(?:https?:\/\/|mailto:|tel:|[\d\s+€.,\/–—·:()]+|[←→↓↗\/B])$/u', $line ) ) {
					return false;
				}

				if ( filter_var( $line, FILTER_VALIDATE_EMAIL ) || preg_match( '/^[A-Z0-9][A-Z0-9 .+&\/-]{1,20}$/u', $line ) ) {
					return false;
				}

				return true;
			}
		)
	)
);

sort( $lines, SORT_NATURAL | SORT_FLAG_CASE );

/**
 * Request one draft translation batch.
 *
 * @return array<int,string>
 */
function translate_batch( array $batch, string $target_language ): array {
	$separator = "\n<<<G5TECH>>>\n";
	$payload   = implode( $separator, $batch );
	$query     = http_build_query(
		array(
			'client' => 'gtx',
			'sl'     => 'lt',
			'tl'     => $target_language,
			'dt'     => 't',
			'q'      => $payload,
		)
	);
	$context   = stream_context_create(
		array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-Type: application/x-www-form-urlencoded\r\nUser-Agent: 5GTECH translation build helper\r\n",
				'content' => $query,
				'timeout' => 60,
			),
		)
	);
	$json      = @file_get_contents( 'https://translate.googleapis.com/translate_a/single', false, $context );
	$data      = $json ? json_decode( $json, true ) : null;

	if ( ! is_array( $data ) || empty( $data[0] ) ) {
		throw new RuntimeException( 'Translation service did not return a valid response.' );
	}

	$translated = '';
	foreach ( $data[0] as $fragment ) {
		$translated .= $fragment[0] ?? '';
	}

	$result = array_map( 'trim', explode( '<<<G5TECH>>>', $translated ) );
	if ( count( $result ) !== count( $batch ) ) {
		throw new RuntimeException( 'Translation batch boundaries were not preserved.' );
	}

	return $result;
}

$catalogue = array();
foreach ( array_chunk( $lines, 16 ) as $batch_index => $batch ) {
	try {
		$translations = translate_batch( $batch, $target_language );
	} catch ( Throwable $error ) {
		$translations = array();
		foreach ( $batch as $line ) {
			$translations[] = translate_batch( array( $line ), $target_language )[0];
			usleep( 100000 );
		}
	}

	foreach ( $batch as $index => $source ) {
		$translation = $translations[ $index ] ?? '';
		if ( '' !== $translation && $translation !== $source ) {
			$catalogue[ $source ] = $translation;
		}
	}

	fwrite( STDERR, sprintf( "Translated batch %d/%d\n", $batch_index + 1, (int) ceil( count( $lines ) / 16 ) ) );
	usleep( 120000 );
}

$php = "<?php\n/**\n * Curated {$target_language} public-copy catalogue.\n * Generated as a draft and reviewed in the project workspace.\n */\n\nreturn " . var_export( $catalogue, true ) . ";\n";

if ( false === file_put_contents( $output_file, $php ) ) {
	fwrite( STDERR, "Could not write output file.\n" );
	exit( 1 );
}

fwrite( STDERR, sprintf( "Wrote %d entries to %s\n", count( $catalogue ), $output_file ) );
