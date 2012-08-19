<?php
namespace Mazagran\Translation\Console\Commands;

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
			->addOption('o', null, InputOption::VALUE_OPTIONAL, 'output folder')
        ;
    }
	
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
		$output->writeln('Compiling file');
		
		$lang = $input->getArgument('lang');
		
		$outputFolder = $input->getOption('o');
		if($outputFolder === null)
		{
			$outputFolder = $this->outputFolder;
		}
		
		$inputFile = $outputFolder.'/'.$lang.'.neon';
		$outputFile = $outputFolder.'/'.$lang.'.dict';
		
		$compiler = new \Mazagran\Translation\Compiler();
		$compiler->compile($inputFile, $outputFile);
		
		$output->writeln(sprintf('<info>Compiled %s to %s.</info>', $inputFile, $outputFile));
	}
}