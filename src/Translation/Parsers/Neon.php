<?php
namespace Translation\Parsers;

use Nette\Utils;
/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Neon implements Parser
{
	private
		/** @var Utils\Neon */	
		$neon
	;

	function __construct()
	{
		$this->neon = new Utils\Neon;
	}

	public function parse($file)
	{
		$input = file_get_contents($file);
		return $this->neon->decode($input);
	}
}