<?php

namespace Bazo\Translation\Console\Commands;

use Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console;

/**
 * Extract
 *
 * @author martin.bazik
 */
class Update extends Command
{

	private $extractDirs;


	public function getExtractDirs()
	{
		return $this->extractDirs;
	}


	public function setExtractDirs($extractDirs)
	{
		$this->extractDirs = $extractDirs;
		return $this;
	}


	protected function configure()
	{
		$this
				->setName('translation:update')
				->setDescription('update translation files')
				->addArgument('lang', InputArgument::OPTIONAL, 'the language for which to generate language file', 'en')
		;
	}


	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$output->writeln('Extracting files');

		$lang = $input->getArgument('lang');


		$data = $extractor->scan($files);

		$outputFile = $outputFolder . '/' . $lang . '.po';

		$builder->buildPo($outputFile, $data);

		$output->writeln(sprintf('<info>Extracted %d tokens. Output saved in: %s.</info>', count($data), $outputFolder));
	}


}

