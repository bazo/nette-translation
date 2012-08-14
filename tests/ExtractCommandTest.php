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
			
		$outputDir	
	;
	
	protected function setUp()
    {
		$app = new \Symfony\Component\Console\Application;
		
		$extractCommand = new Console\Commands\Extract;
		
		$app->add($extractCommand);
		
		$this->app = $app;
		
		$this->dataDir = __DIR__.'/data';
		$this->outputDir = __DIR__.'/output';
    }
	
	public function testExtractCommandRuns()
	{
		
		//var_dump(realpath($this->dataDir.'/header.latte'));exit;
		
		$parameters = array(
			'command' => 'translation:extract',
			'--m' => 'test:test prd:prd',
			'--f' => $this->dataDir.'/header.latte',
			//'--o' => 'php://stdout'
		);
		
		$input = new \Symfony\Component\Console\Input\ArrayInput($parameters);
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		$this->app->find('translation:extract')->run($input, $output);
	}
}