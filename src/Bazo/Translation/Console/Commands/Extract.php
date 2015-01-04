<?php

namespace Bazo\Translation\Console\Commands;


use Bazo\Translation\Builder\TemplateBuilder;
use Bazo\Translation\Extraction\NetteExtractor;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class Extract extends Command
{

	private $extractDirs;
	private $remote = FALSE;
	private $uploader;

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
				->addOption('o', NULL, InputOption::VALUE_OPTIONAL, 'output folder')
				->addOption('f', NULL, InputOption::VALUE_OPTIONAL, 'file to extract, can be specified several times')
				->addOption('r', NULL, InputOption::VALUE_OPTIONAL, 'upload to remote server?')
		;
	}


	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$output->writeln('Extracting files');

		$outputFolder = $input->getOption('o');

		if ($outputFolder === NULL) {
			$outputFolder = $this->outputFolder;
		}

		$files = $input->getOption('f');

		if ($files === NULL) {
			$files = $this->extractDirs;
		}

		if (!isset($files)) {
			$output->writeln('No input files given.');
			exit;
		}

		$remote = $input->getOption('r');

		if ($remote === NULL) {
			$remote = $this->remote;
		} else {
			$remote = (bool) $remote;
		}

		$extractor = new NetteExtractor;

		$extractor->setupForms()->setupDataGrid();
		$data	 = $extractor->scan($files);
		$builder = new TemplateBuilder;
		if ($remote === TRUE) {
			$templateData	 = $builder->formatTemplateData($data);
			$response		 = \Nette\Utils\Json::decode($this->uploader->upload($templateData));
			$output->writeln(sprintf('<info>Extracted %d tokens. Uploaded to remote server. Response: %s</info>', count($data), $response->message));
		} else {
			$outputFile = $outputFolder . '/template.neont';
			$builder->buildTemplate($outputFile, $data);

			$output->writeln(sprintf('<info>Extracted %d tokens. Output saved in: %s.</info>', count($data), $outputFile));
		}
	}


}
