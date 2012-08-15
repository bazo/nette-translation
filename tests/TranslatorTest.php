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
          array('old', 'starý')
        );
    }
	/** @dataProvider messagesProvider */
	public function testTranslateWithGettext($message, $translation)
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