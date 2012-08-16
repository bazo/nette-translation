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
class CreateLangFile extends Console\Command\Command
{

	private
		$extractDirs,
			
		$outputFolder
	;
	
	public function getExtractDirs()
	{
		return $this->extractDirs;
	}

	public function setExtractDirs($extractDirs)
	{
		$this->extractDirs = $extractDirs;
		return $this;
	}

	public function getOutputFolder()
	{
		return $this->outputFolder;
	}

	public function setOutputFolder($outputFolder)
	{
		$this->outputFolder = $outputFolder;
		return $this;
	}
		
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
		
		$inputFile = $outputFolder.'/template.pot';
		$outputFile = $outputFolder.'/'.$lang.'.po';
		
		//$parser = new \Translation\Parsers\Gettext;
		
		$poParser = new \Translation\Parsers\POParser;
		
		$data = $poParser->parse($inputFile);
		
		$pluralRule = \Translation\Langs::getPluralRule($lang);
		
		$builder = new \Translation\Builders\Gettext;
		$builder->setMeta('Language', $pluralRule);
		$builder->buildPo($outputFile, $data);
		
		$output->writeln(sprintf('<info>Created language file for lang: %s. Output saved in: %s.</info>', $lang, $outputFile));
	}
}