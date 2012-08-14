<?php
namespace Translation\Responses;
/**
 * Gettext
 *
 * @author martin.bazik
 */
class Gettext
{

	public
			$metadata = array(),
			$messages = array()

	;

	public function __construct($metadata = array(), $messages = array())
	{
		$this->metadata = $metadata;
		$this->messages = $messages;
	}

}