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
		$expectedIniNeon = array(
			'customer' => 'zákazník',
			'order' => 'objednávka',
			'new' => 'nový',
			'old' => 'starý'
		),
			
		$dataDir	
	;


	protected function setUp()
    {
		$this->dataDir = __DIR__.'/data/dictionaries';
    }
	
	public function testIniFileParsing()
	{
		$parser = new Parsers\Ini;
		$dictionary = $parser->parse($this->dataDir.'/sk.ini');

		$this->assertTrue(is_array($dictionary));
		$this->assertEquals($this->expectedIniNeon, $dictionary);
	}
	
	public function testNeonFileParsing()
	{
		$parser = new Parsers\Neon;
		$dictionary = $parser->parse($this->dataDir.'/sk.neon');
		
		$this->assertTrue(is_array($dictionary));
		$this->assertEquals($this->expectedIniNeon, $dictionary);
	}
	
	public function testGettextFileParsing()
	{
		$parser = new Parsers\Gettext;
		$dictionary = $parser->parse($this->dataDir.'/sk.mo');
		
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