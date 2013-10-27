<?php
namespace Bazo\Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class PluralsTest extends \PHPUnit_Framework_TestCase
{
	
	public function gettextMessagesProvider()
	{
		return array(
			array(1, 0),
			array(2, 1),
			array(5, 2),
		);
	}
	
	/** @dataProvider gettextMessagesProvider */
	public function testSlovakPlurals($count, $pluralExpected)
	{
		$lang = 'sk';
		
		$pluralForm = Langs::getPluralRule($lang);
		$tmp = preg_replace('/([a-z]+)/', '$$1', "n=$count;" . $pluralForm);
		eval($tmp);
		$this->assertEquals($pluralExpected, $plural);
	}
}