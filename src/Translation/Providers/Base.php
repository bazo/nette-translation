<?php
namespace Translation\Providers;
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
		$dictionary = array()
	;
	
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
