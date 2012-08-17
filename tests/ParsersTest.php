<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class ParsersTest extends \PHPUnit_Framework_TestCase
{
	
	private
		$dataDir	
	;

	protected function setUp()
    {
		$this->dataDir = __DIR__.'/data/dictionaries';
    }
	
	public function testParsing()
	{
		$parser = new Parsers\Gettext;
		$dictionary = $parser->parse($this->dataDir.'/sk.mo');
		
		$this->assertTrue(is_array($dictionary));
		$expected = array(
			'Another message!' => array (
				'original' => array (0 => 'Another message!'),
				'translation' => array (0 => 'dalsia sprava')
			),
			'I see %d little indian!' => array(
				'original'=> array(
					0 => 'I see %d little indian!',
					1 => 'I see %d little indians!'
				),
				'translation' => array(
					0 => 'vidim %d maleho indiana',
					1 => 'vidim %d malych indianov',
					2 => 'vidim %d malych indianov'
				)
			),
			'customer' => array(
				'original' => array (0 => 'customer'),
				'translation' => array (0 => 'zákazník')
			),
			'little %s cat jumped on %s' => array(
				'original'=> array(
					0 => 'little %s cat jumped on %s',
					1 => '%d little %s cats jumped on %s'
				),
				'translation'=> array(
					0 => 'mala %s macicka vyskocila na %s',
					1 => '%d male %s macky vyskocili na %s',
					2 =>'%d malych %s maciek vyskocilo na %s'
				),
			),
			'new' => array(
				'original' => array (0 => 'new'),
				'translation' => array (0 => 'nový')  
			),
			'old' => array(
				'original' => array(0 => 'old'),
				'translation' => array(0 => 'starý')
			),
			'order' => array (
				'original' => array (0 => 'order'),
				'translation' => array (0 => 'objednávka')
			)
		);
		
		$this->assertEquals($expected, $dictionary);
	}
}