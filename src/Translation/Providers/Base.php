<?php
namespace Translation\Providers;

use Translation\Parsers\Parser;
/**
 *
 * @author martin.bazik
 */
abstract class Base implements Provider
{
	protected
		/** @var string */
		$lang,
			
		/** @var string */	
		$context,
			
		/** @var array<string|array> */
		$dictionary = array(),
		
		/** @var Parser */	
		$parser,
			
		/** @var array<string> */	
		$dirs
	;
	
	function __construct($dirs, Parser $parser)
	{
		$this->dirs = $dirs;
		$this->parser = $parser;
	}

	
	public function setLang($lang)
	{
		$this->lang = $lang;
		return $this;
	}
	
	public function setContext($context)
	{
		$this->context = $context;
		return $this;
	}
	
	public function translate($message, $count = NULL)
	{
		return $message;
	}
}
