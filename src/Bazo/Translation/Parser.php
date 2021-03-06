<?php

namespace Bazo\Translation;

use Nette\Neon\Neon;



/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Parser
{

	public function parse($file)
	{
		$input = file_get_contents($file);
		return Neon::decode($input);
	}


}
