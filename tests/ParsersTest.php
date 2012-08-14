<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class ParsersTest extends \PHPUnit_Framework_TestCase
{
	
	protected function setUp()
    {
		
    }
	
	public function testIniFileParsing()
	{
		$parser = new Parsers\Ini;
		$dictionary = $parser->parse(__DIR__.'/data/dict.ini');
		$this->assertTrue(is_array($dictionary));
	}
	
	public function testNeonFileParsing()
	{
		$parser = new Parsers\Neon;
		$dictionary = $parser->parse(__DIR__.'/data/dict.neon');
		$this->assertTrue(is_array($dictionary));
	}
	
	public function testGettextFileParsing()
	{
		$parser = new Parsers\Gettext;
		$dictionary = $parser->parse(__DIR__.'/data/sk.mo');
		
		$this->assertTrue(is_array($dictionary));
		
		$expected = array(
			"customer" => array(
				"original" => array (0 => "customer"),
				"translation" => array (0 => "zákazník")
			),
			"new" => array(
				"original" => array (0 => "new"),
				"translation" => array (0 => "nový")  
			),
			"old" => array(
				"original" => array(0 => "old"),
				"translation" => array(0 => "starý")
			),
			"order" => array (
				"original" => array (0 => "order"),
				"translation" => array (0 => "objednávka")
			)
		);
		
		$this->assertEquals($expected, $dictionary);
	}
}