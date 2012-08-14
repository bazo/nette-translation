<?php
namespace Translation\Providers;
/**
 *
 * @author martin.bazik
 */
class Cached extends Base
{
	protected
		$lang,
			
		$context,
			
		$provider
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
	
	/*
	 * 
	 * if(self::$cache)
			{
				$cache->save('dictionary-' . $this->lang, $this->dictionary, array(
					'expire' => time() * 60 * 60 * 2,
					'files' => $files,
					'tags' => array('dictionary-' . $this->lang)
				));
			}
	 */
}
