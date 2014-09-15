<?php

namespace Bazo\Translation\Console\Commands;


use Symfony\Component\Console;

/**
 * @author Martin Bažík <martin@bazik.sk>
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
