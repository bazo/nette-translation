<?php
namespace Translation;
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
		
		$this->potOutput = $this->outputDir.'/dict.pot';
		$this->poOutput = $this->outputDir.'/dict.po';
		
		$inputFile = __DIR__.'/data/filesToScan/default.latte';
		
		$extractor = new Extraction\NetteExtractor;
		$this->data = $extractor->scan($inputFile);
    }
	
	public function tearDown()
	{
		parent::tearDown();
		
		if(file_exists($this->potOutput))
		{
			unlink($this->potOutput);
		}
		
		if(file_exists($this->poOutput))
		{
			unlink($this->poOutput);
		}
	}
	
	public function testGettextBuilderBuildPot()
	{
		$builder = new Builders\Gettext;
		$builder->buildPot($this->potOutput, $this->data);
		
		$this->assertFileExists($this->potOutput);
	}
	
	public function testGettextBuilderBuildPo()
	{
		$builder = new Builders\Gettext;
		$builder->buildPo($this->poOutput, $this->data);
		
		$this->assertFileExists($this->poOutput);
	}
}