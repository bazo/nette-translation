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
			array('A message!', 'Správa!'),
			array('Another message!', 'Ďaľšia správa!'),
			array('I see %d little indian!', 'vidim 1 maleho indiana!', 1),
			array('I see %d little indian!', 'vidim 2 malych indianov!', 2),
			array('I see %d little indian!', 'vidim 5 malych indianov!', 5),
			//array('little %s cat jumped on %s', 'mala cierna macka vyskocila na stol', 1, 'cierna', 'stol'), don't know how to make this work
			array('little %s cat jumped on %s', '2 male cierne macky vyskocili na stol', 2, 'cierne', 'stol'),
			array('little %s cat jumped on %s', '5 malych ciernych maciek vyskocilo na stol', 5, 'ciernych', 'stol'),
		);
	}
	
	/** @dataProvider gettextMessagesProvider */
	public function testTranslate($message, $translation, $count = 1)
	{
		$args = func_get_args();
		
		unset($args[1]); //unset translation
		
		$translator = new Translator($this->dirs);
		$translator->setLang('sk');
		
		$this->assertEquals($translation, call_user_func_array(array($translator, "translate"), $args));
	}
}