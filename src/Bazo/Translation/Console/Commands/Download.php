<?php

namespace Bazo\Translation\Console\Commands;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Martin Bažík <martin@bazik.sk>
 */
class Download extends Command
{

	protected function configure()
	{
		$this
				->setName('translation:download')
				->setDescription('download translations from translation GUI')
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('To be implemented');
	}


}
