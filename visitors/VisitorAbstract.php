<?php
	namespace ThemeCheck\Visitors;

	use PhpParser\NodeVisitorAbstract;

	abstract class VisitorAbstract extends NodeVisitorAbstract
	{
		abstract function IsValid();
		abstract function GetOccurrences();
		abstract function GetError();
		abstract function GetErrorType();
	}