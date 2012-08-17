<?php
namespace Translation;

use Nette\Utils\Neon;
/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Compiler
{
	private
		/** @var Parser */	
		$parser
	;
		
	function __construct()
	{
		$this->parser = new \Translation\Parser;
	}

	
	public function compile($inputFile, $outputFile)
	{
		$data = $this->parser->parse($inputFile);
		$dictionary = new Dictionary($data);
		file_put_contents($outputFile, serialize($dictionary));
	}
}