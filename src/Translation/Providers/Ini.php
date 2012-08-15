<?php
namespace Translation\Providers;
/**
 *
 * @author martin.bazik
 */
class Ini extends Base
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
				if(file_exists($dir . "/" . $this->lang . ".ini"))
				{
					$dictionary = $this->parser->parse($dir . "/" . $this->lang . ".ini");
					$this->dictionary = array_merge($this->dictionary, $dictionary);
				}
			}

			$this->dictionaryLoaded = TRUE;
		}
	}
}
