<?php

namespace Bazo\Translation;

/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{

	/** @var \Symfony\Component\Console\Application */
	private $app;
	private $lang = 'sk';
	private $outputFile;


	protected function setUp()
	{
		$app = new \Symfony\Component\Console\Application;

		$compileCommand = new Console\Commands\Compile;

		$app->add($compileCommand);

		$this->app = $app;

		$this->outputFolder = __DIR__ . '/data/compilation';
		$this->outputFile = $this->outputFolder . '/' . $this->lang . '.dict';
	}


	protected function tearDown()
	{
		parent::tearDown();
		if (file_exists($this->outputFile)) {
			unlink($this->outputFile);
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

