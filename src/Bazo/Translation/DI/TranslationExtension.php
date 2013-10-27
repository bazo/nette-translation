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
		'connect' => FALSE,
		'remoteServer' => NULL,
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
				->setFactory('Bazo\Translation\DI\TranslationExtension::createUploader', array($config));
		
		$builder->addDefinition($this->prefix('translator'))
				->setFactory('Bazo\Translation\DI\TranslationExtension::createTranslator', array('@container', $config));

		$builder->addDefinition($this->prefix('console.commandExtract'))
				->setFactory('Bazo\Translation\DI\TranslationExtension::createConsoleCommandExtract', array('@container', $config))
				->addTag('console.command');

		$builder->addDefinition($this->prefix('console.commandCreateLangFile'))
				->setFactory('Bazo\Translation\DI\TranslationExtension::createConsoleCommandCreateLangFile', array($config))
				->addTag('console.command');

		$builder->addDefinition($this->prefix('console.commandUpdate'))
				->setFactory('Bazo\Translation\DI\TranslationExtension::createConsoleCommandUpdate', array($config))
				->addTag('console.command');
		
		$builder->addDefinition($this->prefix('console.commandCompile'))
				->setFactory('Bazo\Translation\DI\TranslationExtension::createConsoleCommandCompile', array($config))
				->addTag('console.command');

		$builder->addDefinition($this->prefix('panel'))
				->setFactory('Bazo\Translation\Diagnostics\Panel::register');

		if ($config['connect'] === TRUE) {
			$builder->getDefinition('application')->addSetup('$service->onShutdown[] = ?;', [['@translation.translator', 'uploadMessages']]);
		}
	}


	/**
	 * @param \Nette\DI\Container $container
	 * @param array $config
	 * @return \Bazo\Translation\Console\Commands\Extract
	 */
	public static function createConsoleCommandExtract(Container $container, $config)
	{
		$command = new \Bazo\Translation\Console\Commands\Extract;
		$command
			->setExtractDirs($config['scanFile'])
			->setOutputFolder($config['localizationFolder'])
			->setRemote($config['connect'])
			->setUploader($container->getService('translation.uploader'))
		;
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
		$command
			->setExtractDirs($config['scanFile'])
			->setOutputFolder($config['localizationFolder'])
		;
		return $command;
	}
	
	/**
	 * @param array $config
	 * @return \Bazo\Translation\Console\Commands\Compile
	 */
	public static function createConsoleCommandCompile($config)
	{
		$command = new \Bazo\Translation\Console\Commands\Compile;
		$command->setOutputFolder($config['localizationFolder']);
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


	/**
	 * @param array $config
	 * @return \Bazo\Translation\Uploader
	 */
	public static function createUploader($config)
	{
		return new \Bazo\Translation\Uploader($config['remoteServer'], $config['projectId'], $config['secret']);
	}


}

