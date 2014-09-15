<?php

namespace Bazo\Translation;

use Nette\Object;
use Nette\Utils\Finder;

/**
 * Translator
 *
 * @author martin.bazik
 */
class Translator extends Object implements \Nette\Localization\ITranslator
{

	private $lang;
	private $dictionaryLoaded;

	/** @var \Translation\Dictionary */
	private $dictionary;
	private $dir;

	public function __construct($dir)
	{
		$this->dir = $dir;
	}


	public function setLang($lang)
	{
		if ($this->lang === $lang) {
			return;
		}

		$this->lang = $lang;
		$this->dictionary = [];
		$this->dictionaryLoaded = FALSE;
		$this->loadDictionary();
	}


	private function loadDictionary()
	{
		if (!$this->dictionaryLoaded) {
			$dictionaryFile = $this->dir . "/" . $this->lang . ".dict";
			if (file_exists($dictionaryFile)) {
				$this->dictionary = unserialize(file_get_contents($dictionaryFile));
				$this->dictionaryLoaded = TRUE;
			}
		}
	}


	public function translate($message, $count = NULL)
	{
		if (is_null($count)) {
			$count = 1; //fix for form translations
		}

		$this->loadDictionary();
		$message = (string) $message;

		$entry = NULL;

		if ($this->dictionaryLoaded) {
			$entry = $this->dictionary->find($message);
		}

		if ($entry !== NULL) {
			$pluralForm = $this->dictionary->getPluralForm($count);

			if (isset($entry['translations'][$pluralForm])) {
				$message = $entry['translations'][$pluralForm];
			} elseif ($count > 1) {
				$message = $entry['plural'];
			}
		}

		$args = func_get_args();
		if (count($args) > 1) {
			array_shift($args);
			if (is_array(current($args)) || current($args) === NULL) {
				array_shift($args);
			}

			if (count($args) == 1 && is_array(current($args))) {
				$args = current($args);
			}

			$message = str_replace(array("%label", "%name", "%value"), array("#label", "#name", "#value"), $message);
			if (count($args) > 0 && $args != NULL) {
				$message = vsprintf($message, $args);
			}
			$message = str_replace(array("#label", "#name", "#value"), array("%label", "%name", "%value"), $message);
		}

		return $message;
	}


}
