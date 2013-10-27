<?php

namespace Bazo\Translation;

use Bazo\Translation\Extraction\Context;

/**
 * Description of FilterTest
 *
 * @author Ondřej Vodáček
 */
abstract class FilterTest extends \PHPUnit_Framework_TestCase
{

	/** @var AFilter */
	protected $object;

	/** @var string */
	protected $file;


	public function testExtract()
	{
		$messages = $this->object->extract($this->file);

		$this->assertInternalType('array', $messages);

		$this->assertContains([
			Context::LINE => 2,
			Context::SINGULAR => 'A message!'
				], $messages);

		$this->assertContains([
			Context::LINE => 3,
			Context::SINGULAR => 'Another message!',
			Context::CONTEXT => 'context'
				], $messages);

		$this->assertContains([
			Context::LINE => 4,
			Context::SINGULAR => 'I see %d little indian!',
			Context::PLURAL => 'I see %d little indians!'
				], $messages);

		$this->assertContains([
			Context::LINE => 5,
			Context::SINGULAR => 'I see %d little indian!',
			Context::PLURAL => 'I see %d little indians!',
			Context::CONTEXT => 'context'
				], $messages);
	}


}

