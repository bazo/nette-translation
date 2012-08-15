<?php
namespace Translation;
use Translation\Extraction\Extractor;
/**
 * Description of FilterTest
 *
 * @author Ondřej Vodáček
 */
abstract class GettextExtractor_Filters_FilterTest extends \PHPUnit_Framework_TestCase {

	/** @var AFilter */
	protected $object;

	/** @var string */
	protected $file;

	public function testExtract() {
		$messages = $this->object->extract($this->file);

		$this->assertInternalType('array', $messages);

		$this->assertContains(array(
			Extractor::LINE => 2,
			Extractor::SINGULAR => 'A message!'
		), $messages);

		$this->assertContains(array(
			Extractor::LINE => 3,
			Extractor::SINGULAR => 'Another message!',
			Extractor::CONTEXT => 'context'
		), $messages);

		$this->assertContains(array(
			Extractor::LINE => 4,
			Extractor::SINGULAR => 'I see %d little indian!',
			Extractor::PLURAL => 'I see %d little indians!'
		), $messages);

		$this->assertContains(array(
			Extractor::LINE => 5,
			Extractor::SINGULAR => 'I see %d little indian!',
			Extractor::PLURAL => 'I see %d little indians!',
			Extractor::CONTEXT => 'context'
		), $messages);
	}

}
