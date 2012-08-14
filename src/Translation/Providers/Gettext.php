<?php

namespace Translation;

use Nette\Object;

/**
 * Translator
 *
 * @author martin.bazik
 */
class Gettext 
{

	const CACHE_ENABLE = TRUE;
	const CACHE_DISABLE = FALSE;

	/** @var array */
	protected $dirs = array();

	/** @var string */
	protected $lang = "en";

	/** @var array */
	private $metadata;

	/** @var array<string|array> */
	protected $dictionary = array();

	/** @var bool */
	private $loaded = FALSE;

	/** @var bool */
	public static $cache = self::CACHE_DISABLE;

	/**
	 * Load data
	 */
	protected function loadDictonary()
	{
		if(!$this->loaded)
		{
			$cache = Environment::getCache(self::SESSION_NAMESPACE);
			if(self::$cache && isset($cache['dictionary-' . $this->lang]))
				$this->dictionary = $cache['dictionary-' . $this->lang];
			else
			{
				$files = array();
				foreach($this->dirs as $dir)
				{
					if(file_exists($dir . "/" . $this->lang . ".mo"))
					{
						$this->parseFile($dir . "/" . $this->lang . ".mo");
						$file[] = $dir . "/" . $this->lang . ".mo";
					}
				}

				if(self::$cache)
				{
					$cache->save('dictionary-' . $this->lang, $this->dictionary, array(
						'expire' => time() * 60 * 60 * 2,
						'files' => $files,
						'tags' => array('dictionary-' . $this->lang)
					));
				}
			}
			$this->loaded = TRUE;
		}
	}

	public function getDictionary()
	{
		return $this->dictionary;
	}

	public function setDictionary($dictionary)
	{
		$this->dictionary = $dictionary;
		return $this;
	}

	public function getMetadata()
	{
		return $this->metadata;
	}

	public function setMetadata($metadata)
	{
		$this->metadata = $metadata;
		return $this;
	}

	/**
	 * Translates the given string.
	 *
	 * @param string $message
	 * @param int $form plural form (positive number)
	 * @return string
	 */
	public function translate($message, $form = 1)
	{
		$this->loadDictonary();

		$message = (string)$message;
		$message_plural = NULL;
		if(is_array($form) && $form !== NULL)
		{
			$message_plural = current($form);
			$form = end($form);
		}
		if(!is_int($form) || $form === NULL)
		{
			$form = 1;
		}

		if(!empty($message) && isset($this->dictionary[$message]))
		{
			$tmp = preg_replace('/([a-z]+)/', '$$1', "n=$form;" . $this->metadata['Plural-Forms']);
			eval($tmp);


			$message = $this->dictionary[$message]['translation'];
			if(!empty($message))
				$message = (is_array($message) && $plural !== NULL && isset($message[$plural])) ? $message[$plural] : $message;
		} else
		{
			if(!Environment::getHttpResponse()->isSent() || Environment::getSession()->isStarted())
			{
				$space = Environment::getSession(self::SESSION_NAMESPACE);
				if(!isset($space->newStrings))
					$space->newStrings = array();
				$space->newStrings[$message] = empty($message_plural) ? array($message) : array($message, $message_plural);
			}
			if($form > 1 && !empty($message_plural))
				$message = $message_plural;
		}

		if(is_array($message))
			$message = current($message);

		$args = func_get_args();
		if(count($args) > 1)
		{
			array_shift($args);
			if(is_array(current($args)) || current($args) === NULL)
				array_shift($args);

			if(count($args) == 1 && is_array(current($args)))
				$args = current($args);

			$message = str_replace(array("%label", "%name", "%value"), array("#label", "#name", "#value"), $message);
			if(count($args) > 0 && $args != NULL)
				$message = vsprintf($message, $args);
			$message = str_replace(array("#label", "#name", "#value"), array("%label", "%name", "%value"), $message);
		}
		return $message;
	}

	/**
	 * Get count of plural forms
	 *
	 * @return int
	 */
	public function getVariantsCount()
	{
		$this->loadDictonary();

		if(isset($this->metadata['Plural-Forms']))
		{
			return (int)substr($this->metadata['Plural-Forms'], 9, 1);
		}
		return 1;
	}

	

	

	/**
	 * Save dictionary
	 */
	public function save()
	{
		$this->loadDictonary();

		$this->buildMOFile($this->dirs[0] . "/" . $this->lang . ".mo");
		$this->buildPOFile($this->dirs[0] . "/" . $this->lang . ".po");

		$storage = Environment::getSession(self::SESSION_NAMESPACE);
		if(isset($storage->newStrings))
		{
			unset($storage->newStrings);
		}
		if(self::$cache)
		{
			$cache = Environment::getCache(self::SESSION_NAMESPACE)
					->clean(array(\Nette\Caching\Cache::TAGS => 'dictionary-' . $this->lang));
		}
	}

	/**
	 * Generate gettext metadata array
	 *
	 * @return array
	 */
	private function generateMetadata()
	{
		$result = array();
		if(isset($this->metadata['Project-Id-Version']))
			$result[] = "Project-Id-Version: " . $this->metadata['Project-Id-Version'];
		else
			$result[] = "Project-Id-Version: ";
		if(isset($this->metadata['Report-Msgid-Bugs-To']))
			$result[] = "Report-Msgid-Bugs-To: " . $this->metadata['Report-Msgid-Bugs-To'];
		if(isset($this->metadata['POT-Creation-Date']))
			$result[] = "POT-Creation-Date: " . $this->metadata['POT-Creation-Date'];
		else
			$result[] = "POT-Creation-Date: ";
		$result[] = "PO-Revision-Date: " . date("Y-m-d H:iO");
		if(isset($this->metadata['Last-Translator']))
			$result[] = "Language-Team: " . $this->metadata['Language-Team'];
		else
			$result[] = "Language-Team: ";
		if(isset($this->metadata['MIME-Version']))
			$result[] = "MIME-Version: " . $this->metadata['MIME-Version'];
		else
			$result[] = "MIME-Version: 1.0";
		if(isset($this->metadata['Content-Type']))
			$result[] = "Content-Type: " . $this->metadata['Content-Type'];
		else
			$result[] = "Content-Type: text/plain; charset=UTF-8";
		if(isset($this->metadata['Content-Transfer-Encoding']))
			$result[] = "Content-Transfer-Encoding: " . $this->metadata['Content-Transfer-Encoding'];
		else
			$result[] = "Content-Transfer-Encoding: 8bit";
		if(isset($this->metadata['Plural-Forms']))
			$result[] = "Plural-Forms: " . $this->metadata['Plural-Forms'];
		else
			$result[] = "Plural-Forms: ";
		if(isset($this->metadata['X-Poedit-Language']))
			$result[] = "X-Poedit-Language: " . $this->metadata['X-Poedit-Language'];
		if(isset($this->metadata['X-Poedit-Country']))
			$result[] = "X-Poedit-Country: " . $this->metadata['X-Poedit-Country'];
		if(isset($this->metadata['X-Poedit-SourceCharset']))
			$result[] = "X-Poedit-SourceCharset: " . $this->metadata['X-Poedit-SourceCharset'];
		if(isset($this->metadata['X-Poedit-KeywordsList']))
			$result[] = "X-Poedit-KeywordsList: " . $this->metadata['X-Poedit-KeywordsList'];

		return $result;
	}

	/**
	 * Build gettext MO file
	 *
	 * @param string $file
	 */
	private function buildPOFile($file)
	{
		$po = "# Gettext keys exported by GettextTranslator and Translation Panel\n"
				. "# Created: " . date('Y-m-d H:i:s') . "\n" . 'msgid ""' . "\n" . 'msgstr ""' . "\n";
		$po .= '"' . implode('\n"' . "\n" . '"', $this->generateMetadata()) . '\n"' . "\n\n\n";
		foreach($this->dictionary as $message => $data)
		{
			$po .= 'msgid "' . str_replace(array('"', "'"), array('\"', "\\'"), $message) . '"' . "\n";
			if(is_array($data['original']) && count($data['original']) > 1)
				$po .= 'msgid_plural "' . str_replace(array('"', "'"), array('\"', "\\'"), end($data['original'])) . '"' . "\n";
			if(!is_array($data['translation']))
				$po .= 'msgstr "' . str_replace(array('"', "'"), array('\"', "\\'"), $data['translation']) . '"' . "\n";
			elseif(count($data['translation']) < 2)
				$po .= 'msgstr "' . str_replace(array('"', "'"), array('\"', "\\'"), current($data['translation'])) . '"' . "\n";
			else
			{
				$i = 0;
				foreach($data['translation'] as $string)
				{
					$po .= 'msgstr[' . $i . '] "' . str_replace(array('"', "'"), array('\"', "\\'"), $string) . '"' . "\n";
					$i++;
				}
			}
			$po .= "\n";
		}

		$storage = Environment::getSession(self::SESSION_NAMESPACE);
		if(isset($storage->newStrings))
		{
			foreach($storage->newStrings as $original)
			{
				if(trim(current($original)) != "" && !\array_key_exists(current($original), $this->dictionary))
				{
					$po .= 'msgid "' . str_replace(array('"', "'"), array('\"', "\\'"), current($original)) . '"' . "\n";
					if(count($original) > 1)
						$po .= 'msgid_plural "' . str_replace(array('"', "'"), array('\"', "\\'"), end($original)) . '"' . "\n";
					$po .= "\n";
				}
			}
		}

		file_put_contents($file, $po);
	}

	/**
	 * Build gettext MO file
	 *
	 * @param string $file
	 */
	public function buildMOFile($file)
	{
		ksort($this->dictionary);

		$metadata = implode("\n", $this->generateMetadata());
		$items = count($this->dictionary) + 1;
		$ids = String::chr(0x00);
		$strings = $metadata . String::chr(0x00);
		$idsOffsets = array(0, 28 + $items * 16);
		$stringsOffsets = array(array(0, strlen($metadata)));

		foreach($this->dictionary as $key => $value)
		{
			$id = $key;
			if(is_array($value['original']) && count($value['original']) > 1)
				$id .= String::chr(0x00) . end($value['original']);

			$string = implode(String::chr(0x00), $value['translation']);
			$idsOffsets[] = strlen($id);
			$idsOffsets[] = strlen($ids) + 28 + $items * 16;
			$stringsOffsets[] = array(strlen($strings), strlen($string));
			$ids .= $id . String::chr(0x00);
			$strings .= $string . String::chr(0x00);
		}

		$valuesOffsets = array();
		foreach($stringsOffsets as $offset)
		{
			list ($all, $one) = $offset;
			$valuesOffsets[] = $one;
			$valuesOffsets[] = $all + strlen($ids) + 28 + $items * 16;
		}
		$offsets = array_merge($idsOffsets, $valuesOffsets);

		$mo = pack('Iiiiiii', 0x950412de, 0, $items, 28, 28 + $items * 8, 0, 28 + $items * 16);
		foreach($offsets as $offset)
			$mo .= pack('i', $offset);

		file_put_contents($file, $mo . $ids . $strings);
	}

	/**
	 * Get translator
	 *
	 * @param array $options
	 * @return NetteTranslator\Gettext
	 */
	public static function getTranslator($options)
	{
		return new static(isset($options['dir']) ? (array)$options['dir'] : NULL, Environment::getVariable('lang', 'en'));
	}

	/**
	 * Returns current language
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * Sets a new language
	 */
	public function setLang($lang)
	{
		if($this->lang === $lang)
			return;

		$this->lang = $lang;
		$this->dictionary = array();
		$this->loaded = FALSE;
		$this->loadDictonary();
	}

}