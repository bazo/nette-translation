<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class ExtractCommandTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var \Symfony\Component\Console\Application */	
		$app,
			
		$dataDir,
			
		$outputFile,
			
		$lang = 'sk'
	;
	
	protected function setUp()
    {
		$app = new \Symfony\Component\Console\Application;
		
		$extractCommand = new Console\Commands\Extract;
		
		$createVersionCommand = new Console\Commands\CreateLangFile;
		
		$app->add($extractCommand);
		$app->add($createVersionCommand);
		
		$this->app = $app;
		
		$this->dataDir = __DIR__.'/data';
		$this->outputFolder = __DIR__.'/output';
		$this->outputFile = $this->outputFolder.'/template.pot';
    }
	
	protected function tearDown()
	{
		parent::tearDown();
		if(file_exists($this->outputFile))
		{
			//unlink($this->outputFile);
		}
	}
	
	public function testExtractCommandRuns()
	{
		//var_dump(realpath($this->dataDir.'/header.latte'));exit;
		
		$parameters = array(
			'command' => 'translation:extract',
			'--m' => 'test:test prd:prd',
			'--f' => $this->dataDir.'/header.latte',
			'--o' => $this->outputFolder,
			'lang' => $this->lang
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:extract')->run($input, $output);
		
		$this->assertFileExists($this->outputFile);
		 
	}
	
	public function testCreateCommandRuns()
	{
		//var_dump(realpath($this->dataDir.'/header.latte'));exit;
		
		$parameters = array(
			'command' => 'translation:createVersion',
			'--o' => $this->outputFolder,
			'lang' => $this->lang
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:createVersion')->run($input, $output);
		
		//$this->assertFileExists($this->outputFile);
		 
	}
}