<?php

namespace Bazo\Translation\Extraction;


use Bazo\Translation\Extraction\Filters;
use Nette\Utils\Finder;

/**
 * Extractor
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
 * Extractor tool
 *
 * @author Karel Klima
 * @author Ondřej Vodáček
 * @author Martin Bažík <martin@bazik.sk>
 */
class Extractor
{

	/** @var array */
	protected $inputFiles = [];

	/** @var array */
	protected $filters = array(
		'php' => ['PHP']
	);

	/** @var array */
	protected $filterStore = [];

	/** @var array */
	protected $data = [];

	/** @var \Translation\Builders\Builder */
	protected $builder;

	public function __construct()
	{
		$this->addFilter('PHP', new Filters\PHP);
	}


	/**
	 * Exception factory
	 *
	 * @param string $message
	 * @throws Exception
	 */
	protected function throwException($message)
	{
		$message = $message ? $message : 'Something unexpected occured';
		throw new \Exception($message);
	}


	/**
	 * Scans given files or directories and extracts gettext keys from the content
	 *
	 * @param string $directory
	 * @return self
	 */
	public function scan($directory)
	{
		foreach (Finder::findFiles('*.latte', '*.php')->from($directory) as $file) {
			$this->extract($file, $directory);
		}
		return $this->compactMessages();
	}


	protected function extract(\SplFileInfo $file, $directory)
	{
		$fileName	 = substr($file->getPathName(), strlen($directory) + 1);
		$extension	 = $file->getExtension();

		$messages = [];
		foreach ($this->filters[$extension] as $filterName) {
			$filter = $this->getFilter($filterName);

			$filterData = $filter->extract($file->getRealPath());
			if (!is_array($filterData)) {
				continue;
			}
			$messages = array_merge($messages, $filterData);
		}
		$this->data[$fileName] = $messages;
	}


	private function compactMessages()
	{
		$result = [];
		foreach ($this->data as $fileName => $messages) {
			foreach ($messages as $message) {
				$msgId	 = $message[Context::SINGULAR];
				$line	 = $message[Context::LINE];
				$context = isset($message[Context::CONTEXT]) ? $message[Context::CONTEXT] : NULL;

				if (!isset($result[$msgId])) {
					$result[$msgId] = [
						Context::SINGULAR	 => $msgId,
						'translations'		 => [
						],
						'files'				 => []
					];
				} else {
					$result[$msgId]['files'][] = [
						'file'	 => $fileName,
						'line'	 => $line
					];
				}
			}
		}
		
		return $result;
	}


	/**
	 * Gets an instance of a Extractor filter
	 *
	 * @param string $filterName
	 * @return Filters\IFilter
	 */
	public function getFilter($filterName)
	{
		if (isset($this->filterStore[$filterName])) {
			return $this->filterStore[$filterName];
		}
		$this->throwException("ERROR: Filter '$filterName' not found.");
	}


	/**
	 * Assigns a filter to an extension
	 *
	 * @param string $extension
	 * @param string $filterName
	 * @return self
	 */
	public function setFilter($extension, $filterName)
	{
		if (isset($this->filters[$extension]) && in_array($filterName, $this->filters[$extension])) {
			return $this;
		}
		$this->filters[$extension][] = $filterName;
		return $this;
	}


	/**
	 * Add a filter object
	 *
	 * @param type $filterName
	 * @param Filters\IFilter $filter
	 */
	public function addFilter($filterName, Filters\IFilter $filter)
	{
		$this->filterStore[$filterName] = $filter;
	}


	/**
	 * Removes all filter settings in case we want to define a brand new one
	 *
	 * @return self
	 */
	public function removeAllFilters()
	{
		$this->filters = [];
		return $this;
	}


	public function save($outputFile)
	{
		$this->builder->save($outputFile, $this->data);
		return $this;
	}


}
