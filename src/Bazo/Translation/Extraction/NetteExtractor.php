<?php

namespace Bazo\Translation\Extraction;


use Bazo\Translation\Extraction\Filters;

/**
 * GettextExtractor
 *
 * Cool tool for automatic extracting gettext strings for translation
 *
 * Works best with Nette Framework
 *
 * This source file is subject to the New BSD License.
 *
 * @copyright Copyright (c) 2009 Karel Klima
 * @copyright Copyright (c) 2010 Ondřej Vodáček
 * @license New BSD License
 * @package Nette Extras
 */

/**
 * NetteGettextExtractor tool - designed specially for use with Nette Framework
 *
 * @author Karel Klima
 * @author Ondřej Vodáček
 * @author Martin Bažík <martin@bazik.sk>
 * @package Nette Extras
 */
class NetteExtractor extends Extractor
{

	/**
	 * Setup mandatory filters
	 *
	 * @param string|bool $logToFile
	 */
	public function __construct()
	{
		parent::__construct();

		// Clean up...
		$this->removeAllFilters();

		// Set basic filters
		$this
				->setFilter('php', 'PHP')
				->setFilter('latte', 'KdybyLatte')
		;
		$this->addFilter('KdybyLatte', new Filters\KdybyLatte);

		$this->getFilter('PHP')
				->addFunction('t')
				->addFunction('_t')
				->addFunction('_')
				->addFunction('__')
				->addFunction('translate')
				->addFunction('flash')
				->addFunction('flashMessage')
		;
	}


	/**
	 * Optional setup of Forms translations
	 *
	 * @return NetteGettextExtractor
	 */
	public function setupForms()
	{
		$php = $this->getFilter('PHP');
		$php->addFunction('setText')
				->addFunction('setEmptyValue')
				->addFunction('setValue')
				->addFunction('setPrompt')
				->addFunction('addButton', 2)
				->addFunction('addCheckbox', 2)
				->addFunction('addError')
				->addFunction('addFile', 2)
				->addFunction('addUpload', 2)
				->addFunction('addGroup')
				->addFunction('addImage', 3)
				->addFunction('addMultiSelect', 2)
				->addFunction('addPassword', 2)
				->addFunction('addRadioList', 2)
				->addFunction('addRule', 2)
				->addFunction('addSelect', 2)
				->addFunction('addSubmit', 2)
				->addFunction('addText', 2)
				->addFunction('addTextArea', 2)
				->addFunction('addDatePicker', 2)
				->addFunction('addCheckboxList', 2)
				->addFunction('setRequired')
				->addFunction('skipFirst')
				->addFunction('addProtection')
		;

		return $this;
	}


	/**
	 * Optional setup of DataGrid component translations
	 *
	 * @return NetteGettextExtractor
	 */
	public function setupDataGrid()
	{
		$php = $this->getFilter('PHP');
		$php->addFunction('addColumn', 2)
				->addFunction('addNumericColumn', 2)
				->addFunction('addDateColumn', 2)
				->addFunction('addCheckboxColumn', 2)
				->addFunction('addImageColumn', 2)
				->addFunction('addPositionColumn', 2)
				->addFunction('addActionColumn')
				->addFunction('addAction')
		;

		return $this;
	}


}
