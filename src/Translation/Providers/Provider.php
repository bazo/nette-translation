<?php
namespace Translation\Providers;
use Nette\Utils\Neon;
/**
 *
 * @author martin.bazik
 */
class Provider extends Base
{
	protected
		$dictionaryLoaded
	;
	
	public function __construct($dirs)
	{
		parent::__construct($dirs);
	}
	
	protected function loadDictionary()
	{
		if(!$this->dictionaryLoaded)
		{
			foreach($this->dirs as $dir)
			{
				if(file_exists($dir . "/" . $this->lang . ".neon"))
				{
					$dictionary = Neon::decode($dir . "/" . $this->lang . ".neon");
					$this->dictionary = array_merge($this->dictionary, $dictionary);
				}
			}

			$this->dictionaryLoaded = TRUE;
		}
	}
}
