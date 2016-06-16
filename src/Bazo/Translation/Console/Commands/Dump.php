<?php

namespace Bazo\Translation\Console\Commands;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;

/**
 * Extract
 *
 * @author martin.bazik
 */
class Dump extends Command
{

	/** @var \Bazo\Translation\Dumper\TranslationDumper */
	private $dumper;

	public function __construct(\Bazo\Translation\Dumper\TranslationDumper $dumper)
	{
		parent::__construct();
		$this->dumper = $dumper;
	}


	protected function configure()
	{
		$this
				->setName('translation:dump')
				->setDescription('dumps translation files for javscript')
				->addOption('o', NULL, InputOption::VALUE_OPTIONAL, 'output folder')
		;
	}


	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$output->writeln('Dumping files');

		$outputFolder = $input->getOption('o');
		if ($outputFolder === NULL) {
			$outputFolder = $this->outputFolder;
		}

		if (!is_dir($outputFolder)) {
			$output->writeln('<info>[dir+]</info>' . $outputFolder);
			if (false === @mkdir($outputFolder, 0777, true)) {
				throw new \RuntimeException('Unable to create directory ' . $outputFolder);
			}
		}

		$json = $this->dumper->dump();

		file_put_contents($outputFolder . '/translations.json', $json);

		$js = 'var translations = '.$json.';';

		file_put_contents($outputFolder . '/translations.js', $js);
	}


}
