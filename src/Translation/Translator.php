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
			
		$provider
	;
	
	public function translate($message, $count = NULL)
	{
		
	}
}