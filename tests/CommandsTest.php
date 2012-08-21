<?php
namespace Mazagran\Translation;
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
			
		$templateOutputFile,
		$languageOutputFile,
			
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
		$this->templateOutputFile = $this->outputFolder.'/template.neont';
		$this->languageOutputFile = $this->outputFolder.'/'.$this->lang.'.neon';
    }
	
	protected function tearDown()
	{
		parent::tearDown();
		if(file_exists($this->templateOutputFile))
		{
			//unlink($this->templateOutputFile);
		}
		
		if(file_exists($this->languageOutputFile))
		{
			unlink($this->languageOutputFile);
		}
	}
	
	public function testExtractCommandRuns()
	{
		$parameters = array(
			'command' => 'translation:extract',
			'--f' => $this->dataDir.'/test.latte',
			'--o' => $this->outputFolder,
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:extract')->run($input, $output);
		
		$this->assertFileExists($this->templateOutputFile);
		 
	}
	
	public function testCreateCommandRuns()
	{
		$parameters = array(
			'command' => 'translation:extract',
			'--f' => $this->dataDir.'/test.latte',
			'--o' => $this->outputFolder
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:extract')->run($input, $output);
		
		$parameters = array(
			'command' => 'translation:createVersion',
			'--o' => $this->outputFolder,
			'lang' => $this->lang
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:createVersion')->run($input, $output);
		
		$this->assertFileExists($this->languageOutputFile);
		 
	}
	 
}