<?php

namespace Bazo\Translation\Dumper;


use Kdyby\Translation\Translator;
use Nette\Utils\Json;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class TranslationDumper
{

	/** @var Translator */
	private $translator;

	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}


	public function dump()
	{
		$allMessages = [];

		$locales = $this->translator->getAvailableLocales();
		foreach ($locales as $locale) {
			$catalogue					 = $this->translator->getCatalogue($locale);
			$messages					 = $catalogue->all();
			$shortLocale				 = $this->getShortLocale($locale);
			$allMessages[$shortLocale]	 = $messages;
		}

		return $allMessages;
	}


	private function getShortLocale($locale)
	{
		return current(explode('_', $locale));
	}


}
