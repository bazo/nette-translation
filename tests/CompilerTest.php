<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var \Symfony\Component\Console\Application */	
		$app,
			
		$dataDir,

		$lang = 'sk',	
			
		$outputFile
	;
	
	protected function setUp()
    {
		$app = new \Symfony\Component\Console\Application;
		
		$compileCommand = new \Translation\Console\Commands\Compile;
		
		$app->add($compileCommand);
		
		$this->app = $app;
		
		$this->dataDir = __DIR__.'/data';
		$this->outputFolder = __DIR__.'/output';
		$this->outputFile = $this->outputFolder.'/'.$this->lang.'.dict';
    }
	
	protected function tearDown()
	{
		parent::tearDown();
		if(file_exists($this->outputFile))
		{
			//unlink($this->templateOutputFile);
		}
	}
	
	public function testCompileCommandRuns()
	{
		$parameters = array(
			'command' => 'translation:compile',
			'lang' => $this->lang,
			'--o' => $this->outputFolder,
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:compile')->run($input, $output);
		
		$this->assertFileExists($this->outputFile);
		 
	}
	
	
	 
}