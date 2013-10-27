<?php

namespace Bazo\Translation;

/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class ExtractorTest extends \PHPUnit_Framework_TestCase
{

	/** @var Extraction\Extractor */
	private $extractor;
	private $dataDir;


	protected function setUp()
	{
		$this->extractor = new Extraction\Extractor;

		$this->dataDir = __DIR__ . '/data/filesToScan';
		$this->outputDir = __DIR__ . '/output';
	}


	public function testExtractPhpFile()
	{
		$file = $this->dataDir . '/default.php';
		$data = $this->extractor->scan($file);

		$this->assertTrue(is_array($data));
		$this->assertNotEmpty($data);
	}


	public function testExtractLatteFile()
	{
		$file = $this->dataDir . '/default.latte';
		$this->extractor->addFilter('netteLatte', new Extraction\Filters\NetteLatte);
		$data = $this->extractor->scan($file);

		$this->assertTrue(is_array($data));
		//$this->assertNotEmpty($data);
	}


}

