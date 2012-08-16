<?php
namespace Translation\DI;
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
			'dictionaryFolders' => array('%appDir%/l10n'),
			'provider' => 'gettext'
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
		
		$config = $this->getConfig($this->defaults);
		
		$builder->addDefinition($this->prefix('provider'))
			->setFactory('Translation\DI\Extension::createProvider', array($config));
		
		$builder->addDefinition($this->prefix('translator'))
			->setFactory('Translation\DI\Extension::createTranslator', array('@container'));
	}
	
	public static function createProvider($config)
	{
		$providerClass = self::$providerMap[$config['provider']];
		return new $providerClass($config['dictionaryFolders']);
	}
	
	public static function createTranslator(Container $container)
	{
		return new \Translation\Translator($container->translation->provider);
	}
	
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
			$compiler->addExtension('translation', new Extension);
		};
	}
	
}