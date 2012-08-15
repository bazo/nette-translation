<?php
namespace Translation\DI;
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
			'dictionartFolders' => array('%appDir%/l10n'),
			'provider' => 'gettext'
		)
	;

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		
		$config = $this->getConfig($this->defaults);
		
	}
	
	public static function register()
	{
		
	}
	
}