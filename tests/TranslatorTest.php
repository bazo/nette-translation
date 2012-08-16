<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
	private
		$dirs
	;


	protected function setUp()
    {
		$this->dirs = array(
			__DIR__.'/data/dictionaries'
		);
    }
	
	public function messagesProvider()
	{
		return array(
			array('customer', 'zákazník'),
			array('order', 'objednávka'),
			array('new', 'nový'),
			array('old', 'starý'),
		);
	}
	
	public function gettextMessagesProvider()
	{
		return array(
			array('customer', 'zákazník'),
			array('order', 'objednávka'),
			array('new', 'nový'),
			array('old', 'starý'),
			array('I see %d little indian!', 'vidim %d maleho indiana', 1),
			array('I see %d little indian!', 'vidim %d malych indianov', 2),
			array('I see %d little indian!', 'vidim %d malych indianov', 5)
		);
	}
	
	/** @dataProvider gettextMessagesProvider */
	public function testTranslateWithGettext($message, $translation, $count = 1)
	{
		$provider = new Providers\Gettext($this->dirs);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		
		$this->assertEquals($translation, $translator->translate($message));
	}
	
	/** @dataProvider messagesProvider */
	public function testTranslateWithNeon($message, $translation)
	{
		$provider = new Providers\Neon($this->dirs);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		
		$this->assertEquals($translation, $translator->translate($message));
	}
	
	/** @dataProvider messagesProvider */
	public function testTranslateWithIni($message, $translation)
	{
		$provider = new Providers\Ini($this->dirs);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		
		$this->assertEquals($translation, $translator->translate($message));
	}
}