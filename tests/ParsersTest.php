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
		$dictionary = $parser->parse(__DIR__.'/data/dict.mo');
		$this->assertTrue(is_array($dictionary));
	}
}