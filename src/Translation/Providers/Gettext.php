<?php
namespace Translation\Providers;

/**
 * Translator
 *
 * @author martin.bazik
 */
class Gettext extends Base
{

	private
		/** @var bool */
		$loaded = FALSE,
		$metadata = array()
	;

	public function __construct($dirs)
	{
		parent::__construct($dirs);
		$this->parser = new \Translation\Parsers\Gettext;
	}

	/**
	 * Sets a new language
	 */
	public function setLang($lang)
	{
		if ($this->lang === $lang)
			return;

		$this->lang = $lang;
		$this->dictionary = array();
		$this->loaded = FALSE;
		$this->loadDictonary();
	}

	/**
	 * Load data
	 */
	protected function loadDictonary()
	{
		if (!$this->loaded)
		{
			$files = array();
			foreach ($this->dirs as $dir)
			{
				if (file_exists($dir . "/" . $this->lang . ".mo"))
				{
					$dictionary = $this->parser->parseMo($dir . "/" . $this->lang . ".mo");
					$this->dictionary = array_merge($this->dictionary, $dictionary);
					$this->metadata = array_merge($this->metadata, $this->parser->getMetadata());
					$files[] = $dir . "/" . $this->lang . ".mo";
				}
			}

			$this->loaded = TRUE;
		}
	}

	/**
	 * Translates the given string.
	 *
	 * @param string $message
	 * @param int $form plural form (positive number)
	 * @return string
	 */
	public function translate($message, $count = 1)
	{
		$this->loadDictonary();
		$message = (string) $message;

		$entry = $this->dictionary[$message];
		
		$tmp = preg_replace('/([a-z]+)/', '$$1', "n=$count;" . $this->metadata['Plural-Forms']);
		eval($tmp);
		
		$translation = $entry['translation'][$plural];
		return $translation;
	}

	/**
	 * Get count of plural forms
	 *
	 * @return int
	 */
	public function getVariantsCount()
	{
		$this->loadDictonary();

		if (isset($this->metadata['Plural-Forms']))
		{
			return (int) substr($this->metadata['Plural-Forms'], 9, 1);
		}
		return 1;
	}

}