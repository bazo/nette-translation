<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
	
	protected function setUp()
    {
		
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
		$parser = new Parsers\Gettext;
		
		$dirs = array(
			__DIR__.'/data'
		);
		
		$provider = new Providers\Gettext($dirs, $parser);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		$this->assertEquals($translation, $translator->translate($message));
	}
	
	/** @dataProvider messagesProvider */
	public function testTranslateWithNeon($message, $translation)
	{
		$parser = new Parsers\Neon;
		
		$dirs = array(
			__DIR__.'/data'
		);
		
		$provider = new Providers\Neon($dirs, $parser);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		$this->assertEquals($translation, $translator->translate($message));
	}
	
	/** @dataProvider messagesProvider */
	public function testTranslateWithIni($message, $translation)
	{
		$parser = new Parsers\Ini;
		
		$dirs = array(
			__DIR__.'/data'
		);
		
		$provider = new Providers\Ini($dirs, $parser);
		
		$translator = new Translator($provider);
		$translator->setLang('sk');
		$this->assertEquals($translation, $translator->translate($message));
	}
}