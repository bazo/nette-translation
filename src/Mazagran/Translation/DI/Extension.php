<?php
namespace Mazagran\Translation\DI;
use Nette\Config\Configurator;
use Nette\DI\Container;
use Nette\Config\Compiler;
/**
 * Extension
 *
 * @author martin.bazik
 */
class Extension extends \Nette\Config\CompilerExtension
{
	public 
		/** @var array */	
		$defaults = array(
			'scanFile' => '%appDir%',
			'localizationFolder' => '%appDir%/l10n/',
			'connect' => false,
			'projectId' => null,
			'secret' => null,
			'meta' => array(
				'Project-Id-Version' => '',
				'PO-Revision-Date' => '',
				'Last-Translator' => '',
				'Language-Team' => ''
			)
		)
	;

	private static $providerMap = array(
		'gettext' => '\Translation\Providers\Gettext',
		'neon' => '\Translation\Providers\Neon',
		'ini' => '\Translation\Providers\Ini',
	);
	
	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		
		$config = $this->getConfig($this->defaults, true);
		
		$builder->addDefinition($this->prefix('provider'))
			->setFactory('Mazagran\Translation\DI\Extension::createProvider', array($config));
		
		$builder->addDefinition($this->prefix('translator'))
			->setFactory('Mazagran\Translation\DI\Extension::createTranslator', array('@container', $config));
		
		$builder->addDefinition($this->prefix('consoleCommandExtract'))
			->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandExtract', array($config))
			->addTag('consoleCommand');
		
		$builder->addDefinition($this->prefix('consoleCommandCreateLangFile'))
			->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandCreateLangFile', array($config))
			->addTag('consoleCommand');
		
		$builder->addDefinition($this->prefix('consoleCommandUpdate'))
			->setFactory('Mazagran\Translation\DI\Extension::createConsoleCommandUpdate', array($config))
			->addTag('consoleCommand');
		
		$builder->addDefinition($this->prefix('panel'))
			->setFactory('Mazagran\Translation\Diagnostics\Panel::register');
		
		if($config['connect'] === true)
		{
			$builder->getDefinition('application')->addSetup('$service->onShutdown[] = ?;', array(array('@translator', 'uploadMessages')));
		}
	}
	
	public static function createConsoleCommandExtract($config)
	{
		$command = new \Mazagran\Translation\Console\Commands\Extract;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder']);
		return $command;
	}
	
	public static function createConsoleCommandCreateLangFile($config)
	{
		$command = new \Mazagran\Translation\Console\Commands\CreateLangFile;
		$command->setOutputFolder($config['localizationFolder']);
		return $command;
	}
	
	public static function createConsoleCommandUpdate($config)
	{
		$command = new \Mazagran\Translation\Console\Commands\Update;
		$command->setExtractDirs($config['scanFile'])->setOutputFolder($config['localizationFolder']);
		return $command;
	}
	
	public static function createProvider($config)
	{
		$providerClass = self::$providerMap[$config['provider']];
		return new $providerClass($config['localizationFolder']);
	}
	
	public static function createTranslator(Container $container, $config)
	{
		$translator = new \Mazagran\Translation\Translator($config['localizationFolder']);
		if($config['connect'] === true)
		{
			$translator->enableRemoteConnection($config['projectId'], $config['secret']);
		}
		return $translator;
	}
	
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
			$compiler->addExtension('translation', new Extension);
		};
	}
	
}