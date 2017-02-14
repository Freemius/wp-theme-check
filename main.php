<?php
	namespace ThemeCheck;

	require_once __DIR__ . '/vendors/php-parser/lib/bootstrap.php';
	require_once __DIR__ . '/visitors/VisitorAbstract.php';
	require_once __DIR__ . '/visitors/ForbiddenFunctionsVisitor.php';
	require_once __DIR__ . '/FilesProcessor.php';

	$processor = new FilesProcessor();

	$file    = __DIR__ . '/dummy-theme-file.php';
	$content = file_get_contents( $file );

	try {
		$travers_results = $processor->Process( $content, $file );
	} catch ( \Exception $e ) {
		if ( 'php_parse_error' !== $e->getCode() ) {
			throw $e;
		}

		// Handle file parsing error.
	}

	foreach ( $travers_results as $r ) {
		echo $r['type'] . ' : ' . $r['error'] . ":\n";
		foreach ( $r['occurrences'] as $item => $occurrence ) {
			foreach ( $occurrence as $line ) {
				echo "item: {$item}; file: {$file}; line: {$line};\n";
			}
		}
	}