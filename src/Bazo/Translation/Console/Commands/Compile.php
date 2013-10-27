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
				->setDescription('compiles language file')
				->addArgument('lang', InputArgument::OPTIONAL, 'the language for which to generate language file', 'en')
		;
	}


	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$output->writeln('Compiling file');

		$lang = $input->getArgument('lang');

		$outputFolder = $this->outputFolder;

		$inputFile = $outputFolder . '/' . $lang . '.neon';
		
		if(!file_exists($inputFile)) {
			$output->writeln(sprintf('Translation file for language "%s" doesn\'t exist.', $lang));
			return;
		}
		
		$outputFile = $outputFolder . '/' . $lang . '.dict';

		$compiler = new \Bazo\Translation\Compiler();
		$compiler->compile($inputFile, $outputFile);

		$output->writeln(sprintf('<info>Compiled %s to %s.</info>', $inputFile, $outputFile));
	}


}

