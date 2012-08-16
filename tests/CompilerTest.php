<?php
namespace Translation;
/**
 * ExtractCommandTest
 *
 * @author martin.bazik
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var \Translation\Tools\GettextCompiler */	
		$compiler,
			
		$dataDir,
			
		$outputMo,
			
		$outputPo
	;


	protected function setUp()
    {
		$this->compiler = new Tools\GettextCompiler;
		$this->dataDir = __DIR__.'/data/compilation';
		
		$this->outputMo =  $this->dataDir.'/output.mo';
		$this->outputPo =  $this->dataDir.'/output.po';
    }
	/*
	protected function tearDown()
	{
		if(file_exists($this->outputMo))
		{
			unlink($this->outputMo);
		}
		
		if(file_exists($this->outputPo))
		{
			unlink($this->outputPo);
		}
	}
	 * 
	 */
	/*
	public function testCompilePoToMo()
	{
		$inputFile = $this->dataDir.'/input.po';
		
		$this->compiler->compilePo($inputFile, $this->outputMo);
		
		$this->assertFileExists($this->outputMo);
	}
	 * 
	 */
	/*
	public function testDecompileMoToPo()
	{
		$inputFile = $this->dataDir.'/input.mo';
		$this->compiler->decompileMo($inputFile, $this->outputPo);
		
		$this->assertFileExists($this->outputPo);
	}
	*/
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
	public function testMoFileUsable($message, $translation)
	{
		$inputFile = $this->dataDir.'/input.po';
		$moFile = $this->dataDir.'/output.mo';
		
		$this->compiler->compilePo($inputFile, $moFile);
		
		$provider = new Providers\Gettext($this->dataDir);
		
		$translator = new Translator($provider);
		$translator->setLang('output');

		$this->assertEquals($translation, $translator->translate($message));
	}
	
	public function testDontThrowWarning()
	{
		
	}
}