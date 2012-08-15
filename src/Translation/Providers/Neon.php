<?php
namespace Translation\Providers;
/**
 *
 * @author martin.bazik
 */
class Neon extends Base
{
	protected
		$dictionaryLoaded
	;
	
	public function __construct($dirs)
	{
		parent::__construct($dirs);
		$this->parser = new \Translation\Parsers\Neon;
	}
	
	protected function loadDictionary()
	{
		if(!$this->dictionaryLoaded)
		{
			foreach($this->dirs as $dir)
			{
				if(file_exists($dir . "/" . $this->lang . ".neon"))
				{
					$dictionary = $this->parser->parse($dir . "/" . $this->lang . ".neon");
					$this->dictionary = array_merge($this->dictionary, $dictionary);
				}
			}

			$this->dictionaryLoaded = TRUE;
		}
	}
}
