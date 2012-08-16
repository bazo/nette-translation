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
class Extract extends Console\Command\Command
{

	private
		$extractDirs,
			
		$outputFile
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

	public function getOutputFile()
	{
		return $this->outputFile;
	}

	public function setOutputFile($outputFile)
	{
		$this->outputFile = $outputFile;
		return $this;
	}

		
	protected function configure()
    {
        $this
            ->setName('translation:extract')
            ->setDescription('extracts tokens from files')
			->addOption('o', null, InputOption::VALUE_OPTIONAL, 'output file, default output is stdout', 'php://stdout')
			->addOption('l', null, InputOption::VALUE_OPTIONAL, 'log file, default is stderr')
			->addOption('f', null, InputOption::VALUE_OPTIONAL, 'file to extract, can be specified several times')
			->addOption('k', null, InputOption::VALUE_OPTIONAL, "add FUNCTION to filters, format is: \n FILTER:FUNCTION_NAME:SINGULAR,PLURAL,CONTEXT \n default FILTERs are PHP and NetteLatte
						\n for SINGULAR, PLURAL and CONTEXT '0' means not set
						\n can be specified several times")
			->addOption('m', null, InputOption::VALUE_OPTIONAL, 'set meta header')	
        ;
    }
	
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
		$output->writeln('Extracting files');
		
		//$output = 'php://stdout';
		$log = 'php://stderr';
		$keywords = null;
		$meta = null;

		$log = $input->getOption('l');
		$outputFile = $input->getOption('o');
		
		if($outputFile === null)
		{
			$outputFile = $this->outputFile;
		}
		
		$files = $input->getOption('f');
		
		if($files === null)
		{
			$files = $this->extractDirs;
		}
		
		$k = $input->getOption('k');
		$m = $input->getOption('m');
		
		if (!isset($files)) {
			$output->writeln('No input files given.');
			exit;
		}
		
		if ($k) 
		{
			$keywords = array();
			if (is_string($k)) 
			{
				$k = array($k);
			}
			foreach ($k as $value) {
				$filter = $function = $params = null;
				list ($filter, $function, $params) = explode(':', $value);
				$params = explode(',', $params);
				foreach ($params as &$param) {
					$param = (int)$param;
					if ($param === 0) {
						$param = null;
					}
				}
				$keywords[] = array(
					'filter' => $filter,
					'function' => $function,
					'singular' => isset($params[0]) ? $params[0] : null,
					'plural' => isset($params[1]) ? $params[1] : null,
					'context' => isset($params[2]) ? $params[2] : null
				);
			}
		}
		
		if (isset($m)) {
			if (is_string($m)) {
				$m = array($m);
			}
			$key = $value = null;
			foreach ($m as $m) {
				list($key, $value) = explode(':', $m, 2);
				$meta[$key] = $value;
			}
		}

		$extractor = new \Translation\Extraction\NetteExtractor($log);
		$builder = new \Translation\Builders\Gettext;
		
		$extractor->setupForms()->setupDataGrid();
		
		if ($keywords !== null) {
			foreach ($keywords as $value) {
				$extractor->getFilter($value['filter'])
						->addFunction($value['function'], $value['singular'], $value['plural'], $value['context']);
			}
		}
		if ($meta) {
			foreach ($meta as $key => $value) {
				$builder->setMeta($key, $value);
			}
		}
		$data = $extractor->scan($files);
		
		$builder->buildPot($outputFile, $data);
		
		$output->writeln(sprintf('<info>Extracted %d tokens. Output saved in: %s.</info>', count($data), $outputFile));
	}
}