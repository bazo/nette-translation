<?php

namespace Bazo\Translation;

use Nette\Utils\Neon;

/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Compiler
{

	/** @var Parser */
	private $parser;


	function __construct()
	{
		$this->parser = new Parser;
	}


	public function compile($inputFile, $outputFile)
	{
		$data = $this->parser->parse($inputFile);
		$this->compileData($data, $outputFile);
	}


	public function compileData($data, $outputFile)
	{
		$dictionary = new Dictionary($data);
		$this->compileDictionary($dictionary, $outputFile);
	}


	public function compileDictionary(Dictionary $dictionary, $outputFile)
	{
		file_put_contents($outputFile, serialize($dictionary));
	}


}

