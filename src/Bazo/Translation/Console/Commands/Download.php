<?php

namespace Bazo\Translation\Console\Commands;


use Bazo\Translation\Downloader;
use Kdyby\Translation\CatalogueCompiler;
use Nette\DI\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VIPSoft\Unzip\Unzip;

/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class Download extends Command
{

	/** @var Container */
	private $context;

	/** @var Downloader */
	private $downloader;

	public function __construct(Container $context, Downloader $downloader)
	{
		parent::__construct();
		$this->context		 = $context;
		$this->downloader	 = $downloader;
	}


	protected function configure()
	{
		$this
				->setName('translation:download')
				->setDescription('download translations from translation GUI')
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$tempDir = $this->context->parameters['tempDir'];

		//$downloader	 = $this->context->getByType(Downloader::class);
		$zip = $this->downloader->download();

		$tempFile = $tempDir . '/translations.zip';
		file_put_contents($tempFile, $zip);

		$unzip = new Unzip;
		try {
			$unzip->extract($tempFile, $this->outputFolder);
			$catalogueCompiler = $this->context->getByType(CatalogueCompiler::class);
			$catalogueCompiler->invalidateCache();

			$output->writeln('<info>Downloaded</info>');
		} catch (\Exception $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
		} finally {
			unlink($tempFile);
		}
	}


}
