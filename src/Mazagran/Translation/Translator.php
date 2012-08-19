<?php
namespace Mazagran\Translation;

use Nette\Object;

/**
 * Translator
 *
 * @author martin.bazik
 */
class Translator extends Object implements \Nette\Localization\ITranslator
{
	private
		$lang,
			
		$dictionaryLoaded,
		
		/** @var \Translation\Dictionary */	
		$dictionary,
			
		$dirs
	;
	
	function __construct($dirs)
	{
		$this->dirs = $dirs;
	}
	
	public function setLang($lang)
	{
		if ($this->lang === $lang)
			return;

		$this->lang = $lang;
		$this->dictionary = array();
		$this->dictionaryLoaded = FALSE;
		$this->loadDictionary();
	}
		
	private function loadDictionary()
	{
		if(!$this->dictionaryLoaded)
		{
			foreach($this->dirs as $dir)
			{
				$dictionaryFile = $dir . "/" . $this->lang . ".dict";
				if(file_exists($dictionaryFile))
				{
					$this->dictionary = unserialize(file_get_contents($dictionaryFile));
				}
			}

			$this->dictionaryLoaded = TRUE;
		}
	}
	
	public function translate($message, $count = 1)
	{
		$this->loadDictionary();
		$message = (string) $message;

		$entry = $this->dictionary->find($message);
		
		if($entry !== null)
		{
			$pluralForm = $this->dictionary->getPluralForm($count);
			
			if(isset($entry['translations'][$pluralForm]))
			{
				$message = $entry['translations'][$pluralForm];
			}
			elseif($count > 1)
			{
				$message = $entry['plural'];
			}
		}

		$args = func_get_args();
		if (count($args) > 1) {
			array_shift($args);
			if (is_array(current($args)) || current($args) === NULL)
				array_shift($args);

			if (count($args) == 1 && is_array(current($args)))
				$args = current($args);

			$message = str_replace(array("%label", "%name", "%value"), array("#label", "#name", "#value"), $message);
			if (count($args) > 0 && $args != NULL)
			{
				$message = vsprintf($message, $args);
			}
			$message = str_replace(array("#label", "#name", "#value"), array("%label", "%name", "%value"), $message);
		}

		return $message;
	}
	
}