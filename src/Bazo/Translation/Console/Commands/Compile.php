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
class Compile extends Command
{

	protected function configure()
	{
		$this
				->setName('translation:compile')
				->setDescription('compiles translation files for given locale')
				->addArgument('locale', InputArgument::OPTIONAL, 'the locale for which to generate localeuage file', 'en_US')
				->addArgument('defaultDomain', InputArgument::OPTIONAL, 'the default messages domain', 'messages')
				->addOption('o', NULL, InputOption::VALUE_OPTIONAL, 'output folder')
				->addOption('f', NULL, InputOption::VALUE_OPTIONAL, 'force overwrite?', FALSE)
		;
	}


	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$output->writeln('Extracting files');

		$locale = $input->getArgument('locale');
		$defaultDomain = $input->getArgument('defaultDomain');

		$outputFolder = $input->getOption('o');
		if ($outputFolder === NULL) {
			$outputFolder = $this->outputFolder;
		}

		$overwrite = $input->getOption('f');

		$inputFile = $outputFolder . '/template.neont';
		$outputFileMask = $outputFolder . '/%s.' . $locale . '.neon';

		$parser = new \Bazo\Translation\Parser;

		$data = $parser->parse($inputFile);

		$builder = new \Bazo\Translation\Builder\KdybyBuilder;
		$outputFiles = $builder->build($outputFileMask, $data, $defaultDomain, $overwrite);

		$output->writeln(sprintf('<info>Created files for locale: %s. Output saved in: %s.</info>', $locale, implode(', ', $outputFiles)));
	}


}
