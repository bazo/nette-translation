<?php
namespace Mazagran\Translation;

use Nette\Utils\Neon;
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