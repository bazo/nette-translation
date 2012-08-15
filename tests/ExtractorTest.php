<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class ExtractorTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var Extraction\Extractor */	
		$extractor,
			
		$dataDir
	;
	
	protected function setUp()
    {
		$this->extractor = new Extraction\Extractor;
		
		$this->dataDir = __DIR__.'/data';
		$this->outputDir = __DIR__.'/output';
    }
	
	public function testExtractPhpFile()
	{
		$data = $this->extractor->scan($this->dataDir);
	}
}