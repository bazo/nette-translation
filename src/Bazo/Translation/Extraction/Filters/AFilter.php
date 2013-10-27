<?php

namespace Bazo\Translation\Extraction\Filters;

use Bazo\Translation\Extraction\Context;

/**
 * Extractor
 *
 * Cool tool for automatic extracting gettext strings for translation
 *
 * Works best with Nette Framework
 *
 * This source file is subject to the New BSD License.
 *
 * @copyright Copyright (c) 2010 Ondřej Vodáček
 * @license New BSD License
 * @package Nette Extras
 */

/**
 * Abstract filter class.
 *
 * @author Ondřej Vodáček
 */
abstract class AFilter
{

	/** @var array */
	protected $functions = [];


	/**
	 * Includes a function to parse gettext phrases from
	 *
	 * @param $functionName string
	 * @param $singular int
	 * @param $plural int|NULL
	 * @param $context int|NULL
	 * @return AFilter
	 */
	public function addFunction($functionName, $singular = 1, $plural = NULL, $context = NULL)
	{
		if (!is_int($singular) || $singular <= 0) {
			throw new \InvalidArgumentException('Invalid argument type or value given for paramater $singular.');
		}
		$function = array(
			$singular => Context::SINGULAR
		);
		if ($plural !== NULL) {
			if (!is_int($plural) || $plural <= 0) {
				throw new \InvalidArgumentException('Invalid argument type or value given for paramater $plural.');
			}
			$function[$plural] = Context::PLURAL;
		}
		if ($context !== NULL) {
			if (!is_int($context) || $context <= 0) {
				throw new \InvalidArgumentException('Invalid argument type or value given for paramater $context.');
			}
			$function[$context] = Context::CONTEXT;
		}
		$this->functions[$functionName][] = $function;
		return $this;
	}


	/**
	 * Excludes a function from the function list
	 *
	 * @param $functionName
	 * @return AFilter
	 */
	public function removeFunction($functionName)
	{
		unset($this->functions[$functionName]);
		return $this;
	}


	/**
	 * Excludes all functions from the function list
	 *
	 * @return AFilter
	 */
	public function removeAllFunctions()
	{
		$this->functions = [];
		return $this;
	}


	/**
	 * Removes backslashes from before primes and double primes in primed or double primed strings respectively
	 *
	 * @return string
	 * @author Matěj Humpál (https://github.com/finwe)
	 */
	protected function fixEscaping($string)
	{
		$prime = substr($string, 0, 1);
		$string = str_replace('\\' . $prime, $prime, $string);

		return $string;
	}


	/**
	 * Remove single or double quotes from begin and end of the string.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function stripQuotes($string)
	{
		$prime = substr($string, 0, 1);
		if ($prime === "'" || $prime === '"') {
			if (substr($string, -1, 1) === $prime) {
				$string = substr($string, 1, -1);
			}
		}
		return $string;
	}


}

