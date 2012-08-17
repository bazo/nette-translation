<?php
namespace Translation\Console\Commands;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * Extract
 *
 * @author martin.bazik
 */
class CreateLangFile extends Command
{

	protected function configure()
    {
        $this
            ->setName('translation:createVersion')
            ->setDescription('extracts tokens from files')
			->addArgument('lang', InputArgument::OPTIONAL, 'the language for which to generate language file', 'en')
			->addOption('o', null, InputOption::VALUE_OPTIONAL, 'output folder')
        ;
    }
	
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
		$output->writeln('Extracting files');
		
		$lang = $input->getArgument('lang');
		
		if(!\Translation\Langs::verifyLang($lang))
		{
			$output->writeln(sprintf('<error>Language %s is not a valid language</error>', $lang));
		}
		
		$outputFolder = $input->getOption('o');
		if($outputFolder === null)
		{
			$outputFolder = $this->outputFolder;
		}
		
		$inputFile = $outputFolder.'/template.neont';
		$outputFile = $outputFolder.'/'.$lang.'.neon';
		
		$parser = new \Translation\Parser;
		
		$data = $parser->parse($inputFile);
		
		$pluralRule = \Translation\Langs::getPluralRule($lang);
		$pluralCount = \Translation\Langs::getPluralsCount($lang);
		$builder = new \Translation\Builder;
		$builder->addMetadata('plural-count', $pluralCount)->addMetadata('plural-rule', $pluralRule)->addMetadata('creation-date', date('d.m.Y H:i:s'));
		$builder->build($outputFile, $lang, $data);
		
		$output->writeln(sprintf('<info>Created language file for lang: %s. Output saved in: %s.</info>', $lang, $outputFile));
	}
}