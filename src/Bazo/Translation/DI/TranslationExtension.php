<?php

namespace Bazo\Translation\DI;

use Bazo\Translation\Console\Commands\Compile;
use Bazo\Translation\Console\Commands\Download;
use Bazo\Translation\Console\Commands\Dump;
use Bazo\Translation\Console\Commands\Extract;
use Bazo\Translation\Downloader;
use Bazo\Translation\Dumper\TranslationDumper;
use Bazo\Translation\Uploader;

/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class TranslationExtension extends \Nette\DI\CompilerExtension
{

	/** @var array */
	private $defaults = [
		'scanDirs'			 => ['%appDir%'],
		'outputFolder'		 => '%appDir%/lang',
		'jsonOutputFolder'	 => '%appDir%/../www/lang',
		'projectId'			 => NULL,
		'secret'			 => NULL,
		'connect'			 => FALSE,
		'remoteServer'		 => NULL,
		'meta'				 => [
			'Project-Id-Version' => '',
			'PO-Revision-Date'	 => '',
			'Last-Translator'	 => '',
			'Language-Team'		 => ''
		]
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults, TRUE);

		$builder->addDefinition($this->prefix('uploader'))
				->setClass(Uploader::class, [$config['remoteServer'], $config['projectId'], $config['secret']]);

		$builder->addDefinition($this->prefix('downloader'))
				->setClass(Downloader::class, [$config['remoteServer'], $config['projectId'], $config['secret']]);

		$builder->addDefinition($this->prefix('dumper'))
				->setClass(TranslationDumper::class);

		$builder->addDefinition($this->prefix('console.commandExtract'))
				->setClass(Extract::class)
				->addSetup('setUploader', [$this->prefix('@uploader')])
				->addSetup('setExtractDirs', [$config['scanDirs']])
				->addSetup('setOutputFolder', [$config['outputFolder']])
				->addSetup('setRemote', [$config['connect']])
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$builder->addDefinition($this->prefix('console.commandCompile'))
				->setClass(Compile::class)
				->addSetup('setOutputFolder', [$config['outputFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$builder->addDefinition($this->prefix('console.commandDownload'))
				->setClass(Download::class)
				->addSetup('setOutputFolder', [$config['outputFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$builder->addDefinition($this->prefix('console.commandDump'))
				->setClass(Dump::class)
				->addSetup('setOutputFolder', [$config['jsonOutputFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;
	}


}
