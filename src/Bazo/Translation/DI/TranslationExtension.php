<?php

namespace Bazo\Translation\DI;


use Nette\Config\Configurator;
use Nette\DI\Container;
use Nette\Config\Compiler;

/**
 * Extension
 *
 * @author martin.bazik
 */
class TranslationExtension extends \Nette\DI\CompilerExtension
{

	/** @var array */
	private $defaults = [
		'scanFile'			 => '%appDir%',
		'outputFolder' => '%appDir%/l10n',
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

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults, TRUE);

		$builder->addDefinition($this->prefix('uploader'))
				->setClass('Bazo\Translation\Uploader', [$config['remoteServer'], $config['projectId'], $config['secret']]);

		//$builder->addDefinition($this->prefix('translator'))
		//		->setClass('Bazo\Translation\Translator', [$config['localizationFolder']]);

		$builder->addDefinition($this->prefix('console.commandExtract'))
				->setClass(\Bazo\Translation\Console\Commands\Extract::class)
				->addSetup('setUploader', [$this->prefix('@uploader')])
				->addSetup('setExtractDirs', [$config['scanFile']])
				->addSetup('setOutputFolder', [$config['outputFolder']])
				->addSetup('setRemote', [$config['connect']])
				->addTag('console.command')
				->addTag('kdyby.console.command');

		$builder->addDefinition($this->prefix('console.commandCreateLangFile'))
				->setClass(\Bazo\Translation\Console\Commands\CreateLangFile::class)
				->addSetup('setOutputFolder', [$config['outputFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command');
		/*
		$builder->addDefinition($this->prefix('console.commandUpdate'))
				->setFactory('Bazo\Translation\Console\Commands\Update', [$config['scanFile']])
				->addSetup('setOutputFolder', [$config['localizationFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command');

		$builder->addDefinition($this->prefix('console.commandCompile'))
				->setFactory('Bazo\Translation\Console\Commands\Compile', [$config['localizationFolder']])
				->addTag('console.command')
				->addTag('kdyby.console.command');

		if ($config['connect'] === TRUE) {
			$builder->getDefinition('application')->addSetup('$service->onShutdown[] = ?;', [['@'.$this->prefix('uploader'), 'uploadMessages']]);
		}
		 *
		 */
	}




}
