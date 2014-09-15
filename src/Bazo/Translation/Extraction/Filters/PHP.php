<?php

namespace Bazo\Translation\Extraction\Filters;

use Bazo\Translation\Extraction\Context;
use Nette\Utils\Strings;

/**
 * GettextExtractor
 *
 * This source file is subject to the New BSD License.
 *
 * @copyright Copyright (c) 2012 Ondřej Vodáček
 * @license New BSD License
 * @package Nette Extras
 */

/**
 * Filter to fetch gettext phrases from PHP functions
 * @author Ondřej Vodáček
 */
class PHP extends AFilter implements IFilter, \PhpParser\NodeVisitor
{

	/** @var array */
	private $data;


	public function __construct()
	{
		$this->addFunction('gettext', 1);
		$this->addFunction('_', 1);
		$this->addFunction('ngettext', 1, 2);
		$this->addFunction('_n', 1, 2);
		$this->addFunction('pgettext', 2, NULL, 1);
		$this->addFunction('_p', 2, NULL, 1);
		$this->addFunction('npgettext', 2, 3, 1);
		$this->addFunction('_np', 2, 3, 1);
	}


	/**
	 * Parses given file and returns found gettext phrases
	 *
	 * @param string $file
	 * @return array
	 */
	public function extract($file)
	{
		$this->data = [];
		$parser = new \PHPParser\Parser(new \PHPParser\Lexer());
		$stmts = $parser->parse(file_get_contents($file));
		$traverser = new \PHPParser\NodeTraverser();
		$traverser->addVisitor($this);
		$traverser->traverse($stmts);
		$data = $this->data;
		$this->data = NULL;
		return $data;
	}


	public function enterNode(\PHPParser\Node $node)
	{
		$name = NULL;
		if (($node instanceof \PHPParser\Node\Expr_MethodCall || $node instanceof \PHPParser\Node\Expr\StaticCall) && is_string($node->name)) {
			$name = $node->name;
		} elseif ($node instanceof \PHPParser\Node\Expr\FuncCall && $node->name instanceof \PHPParser\Node\Name) {
			$parts = $node->name->parts;
			$name = array_pop($parts);
		} else {
			return;
		}
		if (!isset($this->functions[$name])) {
			return;
		}
		foreach ($this->functions[$name] as $definition) {
			$this->processFunction($definition, $node);
		}
	}


	private function processFunction(array $definition, \PHPParser\Node $node)
	{
		$message = array(
			Context::LINE => $node->getLine()
		);
		foreach ($definition as $position => $type) {
			if (!isset($node->args[$position - 1])) {
				return;
			}
			$arg = $node->args[$position - 1]->value;
			if ($arg instanceof \PHPParser\Node\Scalar\String) {
				$message[$type] = $arg->value;
			} elseif ($arg instanceof \PHPParser\Node\Expr\Array_) {
				foreach ($arg->items as $item) {
					if ($item->value instanceof \PHPParser\Node\Scalar\String) {
						$message[$type][] = $item->value->value;
					}
				}
			} else {
				return;
			}
		}
		if (is_array($message[Context::SINGULAR])) {
			foreach ($message[Context::SINGULAR] as $value) {
				$tmp = $message;
				$tmp[Context::SINGULAR] = Strings::normalize($value);
				$this->data[] = $tmp;
			}
		} else {
			$message[Context::SINGULAR] = Strings::normalize($message[Context::SINGULAR]);
			$this->data[] = $message;
		}
	}


	/*	 * * \PHPParser_NodeVisitor: dont need these ****************************** */

	public function afterTraverse(array $nodes)
	{

	}


	public function beforeTraverse(array $nodes)
	{

	}


	public function leaveNode(\PHPParser\Node $node)
	{

	}


}

