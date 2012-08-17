<?php
namespace Translation\Console\Commands;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * Extract
 *
 * @author martin.bazik
 */
class Extract extends Command
{

	private
		$extractDirs
	;
	
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
            ->setName('translation:extract')
            ->setDescription('extracts tokens from files')
			->addOption('o', null, InputOption::VALUE_OPTIONAL, 'output folder')
			->addOption('f', null, InputOption::VALUE_OPTIONAL, 'file to extract, can be specified several times')
        ;
    }
	
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
		$output->writeln('Extracting files');
		
		$outputFolder = $input->getOption('o');
		
		if($outputFolder === null)
		{
			$outputFolder = $this->outputFolder;
		}
		
		$files = $input->getOption('f');
		
		if($files === null)
		{
			$files = $this->extractDirs;
		}
		
		if (!isset($files)) {
			$output->writeln('No input files given.');
			exit;
		}

		$extractor = new \Translation\Extraction\NetteExtractor;
		
		$extractor->setupForms()->setupDataGrid();
		
		
		$data = $extractor->scan($files);
		
		$outputFile = $outputFolder.'/template.neont';
		
		$builder = new \Translation\Builder;
		$builder->buildTemplate($outputFile, $data);
		
		$output->writeln(sprintf('<info>Extracted %d tokens. Output saved in: %s.</info>', count($data), $outputFile));
	}
}