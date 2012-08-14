<?php
namespace Translation\Providers;

use Nette\Caching\Cache;
/**
 *
 * @author martin.bazik
 */
class Cached extends Base
{
	protected
		/** @var Provider */	
		$provider,
		
		/** @var Cache */	
		$cache
	;
	
	function __construct(Provider $provider, Cache $cache)
	{
		$this->provider = $provider;
		$this->cache = $cache;
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
