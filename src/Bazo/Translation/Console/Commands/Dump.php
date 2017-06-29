<?php

namespace Bazo\Translation\Console\Commands;


use Nette\Utils\Json;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputOption;

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

		$allMessages = $this->dumper->dump();
		$json		 = Json::encode($allMessages, Json::PRETTY);

		file_put_contents($outputFolder . '/translations.json', $json);

		$js = 'var translations = ' . $json . ';';

		$jsFilePath = $outputFolder . '/translations.js';

		$minifier = new \MatthiasMullie\Minify\JS;

		$minifier->add($js);

		$minifier->minify($jsFilePath);
		$minifier->gzip($jsFilePath . '.gz');

		$this->dumpSingleLanguages($allMessages, $outputFolder);
	}


	private function dumpSingleLanguages(array $allMessages, string $outputFolder)
	{
		foreach ($allMessages as $lang => $messages) {
			$data	 = [$lang => $messages];
			$json	 = Json::encode($data, Json::PRETTY);
			$js		 = 'var translations = ' . $json . ';';

			$minifier = new \MatthiasMullie\Minify\JS;

			$minifier->add($js);
			$jsFilePath = $outputFolder . '/translations.' . $lang . '.js';
			$minifier->minify($jsFilePath);
			$minifier->gzip($jsFilePath . '.gz');
		}
	}


}
