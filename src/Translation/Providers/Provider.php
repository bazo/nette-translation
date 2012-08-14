<?php
namespace Translation\Providers;
/**
 *
 * @author martin.bazik
 */
interface Provider
{
	function setLang($lang);
	
	function setContext($context);
	
	function translate($message, $count = NULL);
}
