<?php
namespace Mazagran\Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
	private
		$outputDir,
			
		$potOutput,
			
		$poOutput,
			
		$data = array(
			
		)
	;

	protected function setUp()
    {
		$this->outputDir = __DIR__.'/output';
		
		$this->output = $this->outputDir.'/template.neont';
		
		$inputFile = __DIR__.'/data/filesToScan/default.latte';
		
		$extractor = new Extraction\NetteExtractor;
		$this->data = $extractor->scan($inputFile);
    }
	
	public function tearDown()
	{
		parent::tearDown();
		
		if(file_exists($this->output))
		{
			unlink($this->output);
		}
	}
	
	public function testBuilder()
	{
		$builder = new Builder;
		$builder->buildTemplate($this->output, $this->data);
		
		$this->assertFileExists($this->output);
	}
	
}