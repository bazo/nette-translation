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
		'scanFile' => '%appDir%',
		'localizationFolder' => '%appDir%/l10n/',
		'projectId' => NULL,
		'secret' => NULL,
		'meta' => [
			'Project-Id-Version' => '',
			'PO-Revision-Date' => '',
			'Last-Translator' => '',
			'Language-Team' => ''
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

		$builder->addDefinition($this->prefix('translator'))
				->setFactory('Bazo\Translation\DI\Extension::createTranslator', array('@container', $config));

		$builder->addDefinition($this->prefix('consoleCommandExtract'))
				->setFactory('Bazo\Translation\DI\Extension::createConsoleCommandExtract', array('@container', $config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('consoleCommandCreateLangFile'))
				->setFactory('Bazo\Translation\DI\Extension::createConsoleCommandCreateLangFile', array($config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('consoleCommandUpdate'))
				->setFactory('Bazo\Translation\DI\Extension::createConsoleCommandUpdate', array($config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('panel'))
				->setFactory('Bazo\Translation\Diagnostics\Panel::register');
	}


	/**
	 * @param \Nette\DI\Container $container
	 * @param array $config
	 * @return \Bazo\Translation\Console\Commands\Extract
	 */
	public static function createConsoleCommandExtract(Container $container, $config)
	{
		$command = new \Bazo\Translation\Console\Commands\Extract;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder'])->setRemote($config['connect'])
				->setUploader($container->getService('translation.uploader'));
		return $command;
	}


	/**
	 * @param array $config
	 * @return \Bazo\Translation\Console\Commands\CreateLangFile
	 */
	public static function createConsoleCommandCreateLangFile($config)
	{
		$command = new \Bazo\Translation\Console\Commands\CreateLangFile;
		$command->setOutputFolder($config['localizationFolder']);
		return $command;
	}


	/**
	 * @param array $config
	 * @return \Bazo\Translation\Console\Commands\Update
	 */
	public static function createConsoleCommandUpdate($config)
	{
		$command = new \Bazo\Translation\Console\Commands\Update;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder']);
		return $command;
	}


	/**
	 * @param \Nette\DI\Container $container
	 * @param array $config
	 * @return \Bazo\Translation\Translator
	 */
	public static function createTranslator(Container $container, $config)
	{
		$translator = new \Bazo\Translation\Translator($config['localizationFolder']);
		return $translator;
	}


}

