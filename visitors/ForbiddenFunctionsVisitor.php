<?php
	namespace ThemeCheck\Visitors;

	use PhpParser\Node;

	class ForbiddenFunctions extends VisitorAbstract {

		const TYPE  = 'ERROR';
		const ERROR = 'Usage of forbidden functions';

		private static $_FORBIDDEN_FUNCTIONS = array(
			'eval',
			'ini_set',
			'popen',
			'proc_open',
			'exec',
			'shell_exec',
			'system',
			'passthru',
			'base64_decode',
			'base64_encode',
			'uudecode',
			'str_rot13',
		);

		private $_occurrences = array();

		function IsValid() {
			return empty( $this->_occurrences );
		}

		function GetOccurrences() {
			return $this->_occurrences;
		}

		function GetError() {
			return self::ERROR;
		}

		function GetErrorType() {
			return self::TYPE;
		}

		function leaveNode( Node $node ) {
			if ( $node instanceof Node\Expr\FuncCall &&
			     in_array( $node->name->parts[0], self::$_FORBIDDEN_FUNCTIONS )
			) {
				$fun_name = $node->name->parts[0];

				if ( ! isset( $this->_occurrences[ $fun_name ] ) ) {
					$this->_occurrences[ $fun_name ] = array();
				}

				$this->_occurrences[ $fun_name ][] = $node->getLine();
			}

			return $node;
		}
	}