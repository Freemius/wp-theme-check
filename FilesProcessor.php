<?php
	namespace ThemeCheck;

	use PhpParser\Error;
	use PhpParser\Lexer\Emulative;
	use PhpParser\NodeTraverser;
	use PhpParser\Parser;
	use PhpParser\PrettyPrinter\Standard;
	use ThemeCheck\Visitors\ForbiddenFunctions;
	use ThemeCheck\Visitors\VisitorAbstract;

	class FilesProcessor {
		private $_parser;
		private $_prettyPrinter;
		private $_traverser;
		/**
		 * @var VisitorAbstract[]
		 */
		private $_visitors;

		function __construct() {
			$this->_parser        = new Parser( new Emulative() );
			$this->_prettyPrinter = new Standard();
			$this->_traverser     = new NodeTraverser();

			$this->_visitors = array(
				new ForbiddenFunctions(),
			);

			foreach ( $this->_visitors as $visitor ) {
				$this->_traverser->addVisitor( $visitor );
			}
		}

		private function GetTraverseResult() {
			$result = array();

			foreach ( $this->_visitors as $visitor ) {
				if ( ! $visitor->IsValid() ) {
					$result[] = array(
						'error' => $visitor->GetError(),
						'type' => $visitor->GetErrorType(),
						'occurrences' => $visitor->GetOccurrences(),
					);
				}
			}

			return $result;
		}

		function Process( &$php_file_content, $file_relative_path ) {
			try {
				$statements = $this->_parser->parse( $php_file_content );
				$this->_traverser->traverse( $statements );
			} catch ( Error $e ) {
				throw new \Exception( 'Failed parsing PHP file ' . $file_relative_path . ': ' . $e->getMessage(), 'php_parse_error' );
			}

			return $this->GetTraverseResult();
		}
	}