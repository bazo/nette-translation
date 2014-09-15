<?php

namespace Bazo\Translation\Console\Commands;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class Upload extends Command
{

	protected function configure()
	{
		$this
				->setName('translation:upload')
				->setDescription('upload template to translation GUI')
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('To be implemented');
	}


}
