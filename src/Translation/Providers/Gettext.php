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

	
	
	/**
	 * Load data
	 */
	protected function loadDictonary()
	{
		if(!$this->loaded)
		{
			$files = array();
			foreach($this->dirs as $dir)
			{
				if(file_exists($dir . "/" . $this->lang . ".mo"))
				{
					$dictionary = $this->parser->parse($dir . "/" . $this->lang . ".mo");
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