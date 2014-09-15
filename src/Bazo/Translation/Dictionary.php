<?php

namespace Bazo\Translation;

use Nette\Object;



/**
 * Translator
 *
 * @author martin.bazik
 */
class Dictionary extends Object
{

	private $pluralsCount;
	private $pluralRule;
	private $metadata;
	private $messagesCatalog;
	private $contextCatalog;
	private $lang;



	public function __construct($data)
	{
		$this->metadata = $data['metadata'];
		$this->lang = $data['lang'];
		$this->pluralsCount = $data['metadata']['plural-count'];
		$this->pluralRule = $data['metadata']['plural-rule'];

		$contextCatalog = [];

		foreach ($data['messages'] as $id => $message) {
			if ($message['translations'][0] === '') {
				continue;
			}
			if (isset($message['files'])) {
				unset($message['files']);
			}
			if (isset($message['context'])) {
				$contextCatalog[$message['context']][$id] = $message;
			}
			$this->messagesCatalog[$id] = $message;
		}
		$this->contextCatalog = $contextCatalog;
	}


	public function find($id)
	{
		return isset($this->messagesCatalog[$id]) ? $this->messagesCatalog[$id] : null;
	}


	public function getPluralsCount()
	{
		return $this->pluralsCount;
	}


	public function getPluralRule()
	{
		return $this->pluralRule;
	}


	public function getPluralForm($count)
	{
		$tmp = preg_replace('/([a-z]+)/', '$$1', "n=$count;" . $this->pluralRule);
		eval($tmp);
		return $plural;
	}


	public function getMetadata()
	{
		return $this->metadata;
	}


	public function getLang()
	{
		return $this->lang;
	}


}
