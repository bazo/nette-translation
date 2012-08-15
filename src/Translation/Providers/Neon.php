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
