<?php

namespace Bazo\Translation\Extraction\Filters;

use Bazo\Translation\Extraction\Context;
use Nette\Utils\Strings;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser\Multiple;
use PhpParser\Parser\Php5;
use PhpParser\Parser\Php7;

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
class PHP extends \Bazo\Translation\Extraction\Filters\AFilter implements \Bazo\Translation\Extraction\Filters\IFilter, NodeVisitor
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
		$this->data	 = [];
		$lexer		 = new Lexer;
		//$parser		 = new Parser($lexer);

		$php5Parser	 = new Php5($lexer);
		$php7Parser	 = new Php7($lexer);

		$parsers = [
			$php5Parser,
			$php7Parser
		];

		$parser = new Multiple($parsers);

		$stmts		 = $parser->parse(file_get_contents($file));
		$traverser	 = new NodeTraverser();
		$traverser->addVisitor($this);
		$traverser->traverse($stmts);
		$data		 = $this->data;
		$this->data	 = NULL;
		return $data;
	}


	public function enterNode(Node $node)
	{
		$name = NULL;

		if (($node instanceof MethodCall || $node instanceof StaticCall) && is_string($node->name)) {
			$name = $node->name;
		} elseif ($node instanceof FuncCall && $node->name instanceof Name) {
			$parts	 = $node->name->parts;
			$name	 = array_pop($parts);
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


	private function processFunction(array $definition, Node $node)
	{
		$message = array(
			Context::LINE => $node->getLine()
		);
		foreach ($definition as $position => $type) {
			if (!isset($node->args[$position - 1])) {
				return;
			}
			$arg = $node->args[$position - 1]->value;
			if ($arg instanceof String_) {
				$message[$type] = $arg->value;
			} elseif ($arg instanceof Array_) {
				foreach ($arg->items as $item) {
					if ($item->value instanceof String_) {
						$message[$type][] = $item->value->value;
					}
				}
			} else {
				return;
			}
		}
		if (is_array($message[Context::SINGULAR])) {
			foreach ($message[Context::SINGULAR] as $value) {
				$tmp					 = $message;
				$tmp[Context::SINGULAR]	 = Strings::normalize($value);
				$this->data[]			 = $tmp;
			}
		} else {
			$message[Context::SINGULAR]	 = Strings::normalize($message[Context::SINGULAR]);
			$this->data[]				 = $message;
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
