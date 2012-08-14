<?php
namespace Translation\Parsers;
/**
 * GettextParser
 *
 * @author Martin Bažík
 */
interface Parser
{
	public function parse($file);
}