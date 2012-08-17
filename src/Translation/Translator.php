<?php
namespace Translation;

use Nette\Object;

/**
 * Translator
 *
 * @author martin.bazik
 */
class Translator extends Object implements \Nette\Localization\ITranslator
{
	protected
		$lang,
		
		/** @var \Translation\Providers\Provider */	
		$provider
	;
	
	function __construct($provider)
	{
		$this->provider = $provider;
	}

	public function setLang($lang)
	{
		$this->lang = $lang;
		$this->provider->setLang($lang);
		return $this;
	}
		
	public function translate($message, $count = 1)
	{
		return call_user_func_array(array($this->provider, 'translate'), func_get_args());
	}
}