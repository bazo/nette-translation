<?php

namespace Translation\Extraction;
use Translation\Extraction\Filters;
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
 */
class Extractor
{

	const ESCAPE_CHARS = '"';
	const OUTPUT_PO = 'PO';
	const OUTPUT_POT = 'POT';
	const CONTEXT = 'context';
	const SINGULAR = 'singular';
	const PLURAL = 'plural';
	const LINE = 'line';
	const FILE = 'file';


	/** @var array */
	protected $inputFiles = array();

	/** @var array */
	protected $filters = array(
		'php' => array('PHP')
	);

	/** @var array */
	protected $filterStore = array();

	/** @var array */
	protected $comments = array(
		'Keys exported by Extractor'
	);


	/** @var array */
	protected $data = array();

	/** @var string */
	protected $outputMode = self::OUTPUT_PO;

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
		throw new Exception($message);
	}

	/**
	 * Scans given files or directories and extracts gettext keys from the content
	 *
	 * @param string|array $resource
	 * @return self
	 */
	public function scan($resource)
	{
		$this->inputFiles = array();
		if(!is_array($resource))
		{
			$resource = array($resource);
		}
		foreach($resource as $item)
		{
			$this->_scan($item);
		}
		return $this->_extract($this->inputFiles);
		return $this;
	}

	/**
	 * Scans given files or directories (recursively)
	 *
	 * @param string $resource File or directory
	 */
	protected function _scan($resource)
	{
		if(is_file($resource))
		{
			$this->inputFiles[] = $resource;
		}
		elseif(is_dir($resource))
		{
			$iterator = new \RecursiveIteratorIterator(
							new \RecursiveDirectoryIterator($resource, \RecursiveDirectoryIterator::SKIP_DOTS)
			);
			foreach($iterator as $file)
			{
				$this->inputFiles[] = $file->getPathName();
			}
		}
		else
		{
			$this->throwException("Resource '$resource' is not a directory or file");
		}
	}

	/**
	 * Extracts gettext keys from input files
	 *
	 * @param array $inputFiles
	 * @return array
	 */
	protected function _extract($inputFiles)
	{
		$inputFiles = array_unique($inputFiles);
		sort($inputFiles);
		foreach($inputFiles as $inputFile)
		{
			if(!file_exists($inputFile))
			{
				$this->throwException('ERROR: Invalid input file specified: ' . $inputFile);
			}
			if(!is_readable($inputFile))
			{
				$this->throwException('ERROR: Input file is not readable: ' . $inputFile);
			}

			$fileExtension = pathinfo($inputFile, PATHINFO_EXTENSION);
			foreach($this->filters as $extension => $filters)
			{
				// Check file extension
				if($fileExtension !== $extension)
					continue;

				foreach($filters as $filterName)
				{
					$filter = $this->getFilter($filterName);
					$filterData = $filter->extract($inputFile);
					$this->addMessages($filterData, $inputFile);
				}
			}
		}
		return $this->data;
	}

	/**
	 * Gets an instance of a Extractor filter
	 *
	 * @param string $filterName
	 * @return Filters\IFilter
	 */
	public function getFilter($filterName)
	{
		if(isset($this->filterStore[$filterName]))
		{
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
		if(isset($this->filters[$extension]) && in_array($filterName, $this->filters[$extension]))
			return $this;
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
		$this->filters = array();
		return $this;
	}

	/**
	 * Adds a comment to the top of the output file
	 *
	 * @param string $value
	 * @return self
	 */
	public function addComment($value)
	{
		$this->comments[] = $value;
		return $this;
	}

	/**
	 * Gets a value of a meta key
	 *
	 * @param string $key
	 */
	public function getMeta($key)
	{
		return isset($this->meta[$key]) ? $this->meta[$key] : NULL;
	}

	/**
	 * Sets a value of a meta key
	 *
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function setMeta($key, $value)
	{
		$this->meta[$key] = $value;
		return $this;
	}

	protected function addMessages(array $messages, $file)
	{
		foreach($messages as $message)
		{
			$key = '';
			if(isset($message[self::CONTEXT]))
			{
				$key .= $message[self::CONTEXT];
			}
			$key .= chr(4);
			$key .= $message[self::SINGULAR];
			$key .= chr(4);
			if(isset($message[self::PLURAL]))
			{
				$key .= $message[self::PLURAL];
			}
			if($key === chr(4) . chr(4))
			{
				continue;
			}
			$line = $message[self::LINE];
			if(!isset($this->data[$key]))
			{
				unset($message[self::LINE]);
				$this->data[$key] = $message;
				$this->data[$key]['files'] = array();
			}
			$this->data[$key]['files'][] = array(
				self::FILE => $file,
				self::LINE => $line
			);
		}
	}
}
