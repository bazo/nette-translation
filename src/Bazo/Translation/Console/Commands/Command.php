<?php

namespace Bazo\Translation\Console\Commands;

use Symfony\Component\Console;



/**
 * Extract
 *
 * @author martin.bazik
 */
abstract class Command extends Console\Command\Command
{

	protected $outputFolder;



	public function getOutputFolder()
	{
		return $this->outputFolder;
	}


	public function setOutputFolder($outputFolder)
	{
		$this->outputFolder = $outputFolder;
		return $this;
	}


}
