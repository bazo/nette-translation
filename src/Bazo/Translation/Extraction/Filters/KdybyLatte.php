<?php

namespace Bazo\Translation\Extraction\Filters;


use Latte\Parser;
use Latte\MacroTokens;
use Latte\PhpWriter;

/**
 * @author Filip Procházka <filip@prochazka.su>
 * @author Martin Bažík <martin@bazo.sk>
 */
class KdybyLatte extends AFilter implements IFilter
{

	/** @var string */
	private $prefix;

	/** @var array */
	private $messages = [];

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}


	public function extract($file)
	{
		$this->messages	 = [];
		$buffer			 = NULL;
		$parser			 = new Parser();
		foreach ($tokens			 = $parser->parse(file_get_contents($file)) as $token) {
			$line = $token->line;
			if ($token->type !== $token::MACRO_TAG || !in_array($token->name, array('_', '/_'), TRUE)) {
				if ($buffer !== NULL) {
					$buffer .= $token->text;
				}

				continue;
			}

			if ($token->name === '/_') {
				$this->add(($this->prefix ? $this->prefix . '.' : '') . $buffer, $line);
				$buffer = NULL;
			} elseif ($token->name === '_' && empty($token->value)) {
				$buffer = '';
			} else {
				$args	 = new MacroTokens($token->value);
				$writer	 = new PhpWriter($args, $token->modifiers);

				$message = $writer->write('%node.word');
				if (in_array(substr(trim($message), 0, 1), array('"', '\''), TRUE)) {
					$message = substr(trim($message), 1, -1);
				}

				$this->add(($this->prefix ? $this->prefix . '.' : '') . $message, $line);
			}
		}

		return $this->messages;
	}


	public function add($id, $line)
	{
		if (\Nette\Utils\Strings::startsWith($id, '$')) {
			return;
		}

		$this->messages[$id] = [
			'line'	 => $line,
			'msgid'	 => $id
		];
	}


}
