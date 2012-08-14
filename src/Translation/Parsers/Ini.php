<?php
namespace Translation\Parsers;
/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Ini implements Parser
{
	public function parse($file)
	{
		return parse_ini_file($file);
	}
}