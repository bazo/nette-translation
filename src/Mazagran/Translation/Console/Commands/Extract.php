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
class Extract extends Command
{

	private
		$extractDirs,
			
		$remote = false,
			
		$uploader
	;
	
	public function setUploader($uploader)
	{
		$this->uploader = $uploader;
		return $this;
	}
		
	public function setRemote($remote)
	{
		$this->remote = $remote;
		return $this;
	}
	
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
			->addOption('r', null, InputOption::VALUE_OPTIONAL, 'upload to remote server?')	
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

		$remote = (bool)$input->getOption('r');
		
		if($remote === null)
		{
			$remote = $this->remote;
		}
		
		$extractor = new \Mazagran\Translation\Extraction\NetteExtractor;
		
		$extractor->setupForms()->setupDataGrid();
		$data = $extractor->scan($files);
		$builder = new \Mazagran\Translation\Builder;
		if($remote === true)
		{
			$templateData = $builder->formatTemplateData($data);
			$this->uploader->upload($templateData);
			$output->writeln(sprintf('<info>Extracted %d tokens. Uploaded to remote server.</info>', count($data)));
		}
		else
		{
			$outputFile = $outputFolder.'/template.neont';
			$builder->buildTemplate($outputFile, $data);

			$output->writeln(sprintf('<info>Extracted %d tokens. Output saved in: %s.</info>', count($data), $outputFile));
		}
		
	}
}