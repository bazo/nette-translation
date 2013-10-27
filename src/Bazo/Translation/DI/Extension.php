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
		'connect' => FALSE,
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

		$builder->addDefinition($this->prefix('uploader'))
				->setFactory('Mazagran\Translation\DI\Extension::createUploader', array($config));

		$builder->addDefinition($this->prefix('translator'))
				->setFactory('Mazagran\Translation\DI\Extension::createTranslator', array('@container', $config));

		$builder->addDefinition($this->prefix('consoleCommandExtract'))
				->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandExtract', array('@container', $config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('consoleCommandCreateLangFile'))
				->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandCreateLangFile', array($config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('consoleCommandUpdate'))
				->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandUpdate', array($config))
				->addTag('consoleCommand');

		$builder->addDefinition($this->prefix('panel'))
				->setFactory('Mazagran\Translation\Diagnostics\Panel::register');

		if ($config['connect'] === TRUE) {
			$builder->getDefinition('application')->addSetup('$service->onShutdown[] = ?;', array(array('@translation.translator', 'uploadMessages')));
		}
	}


	/**
	 * @param \Nette\DI\Container $container
	 * @param array $config
	 * @return \Mazagran\Translation\Console\Commands\Extract
	 */
	public static function createConsoleCommandExtract(Container $container, $config)
	{
		$command = new \Mazagran\Translation\Console\Commands\Extract;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder'])->setRemote($config['connect'])
				->setUploader($container->getService('translation.uploader'));
		return $command;
	}


	/**
	 * @param array $config
	 * @return \Mazagran\Translation\Console\Commands\CreateLangFile
	 */
	public static function createConsoleCommandCreateLangFile($config)
	{
		$command = new \Mazagran\Translation\Console\Commands\CreateLangFile;
		$command->setOutputFolder($config['localizationFolder']);
		return $command;
	}


	/**
	 * @param array $config
	 * @return \Mazagran\Translation\Console\Commands\Update
	 */
	public static function createConsoleCommandUpdate($config)
	{
		$command = new \Mazagran\Translation\Console\Commands\Update;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder']);
		return $command;
	}


	/**
	 * @param array $config
	 * @return \Mazagran\Translation\Uploader
	 */
	public static function createUploader($config)
	{
		return new \Mazagran\Translation\Uploader($config['projectId'], $config['secret']);
	}


	/**
	 * @param \Nette\DI\Container $container
	 * @param array $config
	 * @return \Mazagran\Translation\Translator
	 */
	public static function createTranslator(Container $container, $config)
	{
		$translator = new \Mazagran\Translation\Translator($config['localizationFolder']);
		if ($config['connect'] === TRUE) {
			$translator->enableRemoteConnection($config['projectId'], $config['secret']);
		}
		return $translator;
	}


}

