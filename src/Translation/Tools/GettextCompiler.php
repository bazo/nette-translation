<?php
namespace Translation\Tools;

use Translation\Extraction\Context;
use Nette\Utils\Strings;

/**
 * Compiler
 *
 * @author martin.bazik
 */
class GettextCompiler
{

	private
		/** @var \Translation\Parsers\Gettext */
		$parser

	;

	public function __construct()
	{
		$this->parser = new \Translation\Parsers\Gettext;
	}

	public function compilePo($poFile, $moFile)
	{
		$data = $this->parser->parsePo($poFile);
		$this->buildMOFile($data, $this->parser->getMetadata(), $moFile);
		return $this;
	}

	public function decompileMo($moFile, $poFile)
	{
		$data = $this->parser->parse($moFile);
		$this->buildPOFile($data, $this->parser->getMetadata(), $poFile);
		return $this;
	}

	private function buildMOFile($data, $metadata, $file)
	{
		ksort($data);

		$metadata = implode("\n", $this->generateMetadata($metadata));
		$items = count($data) + 1;
		$ids = Strings::chr(0x00);
		$strings = $metadata . Strings::chr(0x00);
		$idsOffsets = array(0, 28 + $items * 16);
		$stringsOffsets = array(array(0, strlen($metadata)));

		foreach ($data as $entry)
		{
			$id = $entry[Context::SINGULAR];

			if(isset($entry[Context::PLURAL]))
			{
				$id .= "\x00" . $entry[Context::PLURAL];
			}
			
			$translation = $entry[Context::TRANSLATION] === false ? '' : $entry[Context::TRANSLATION];
			$string = implode("\x00", $translation);
			/*
			if (is_array($value['original']) && count($value['original']) > 1)
				$id .= Strings::chr(0x00) . end($value['original']);
			*/
			$idsOffsets[] = strlen($id);
			$idsOffsets[] = strlen($ids) + 28 + $items * 16;
			$stringsOffsets[] = array(strlen($strings), strlen($string));
			$ids .= $id . Strings::chr(0x00);
			$strings .= $string . Strings::chr(0x00);
		}

		$valuesOffsets = array();
		foreach ($stringsOffsets as $offset)
		{
			list ($all, $one) = $offset;
			$valuesOffsets[] = $one;
			$valuesOffsets[] = $all + strlen($ids) + 28 + $items * 16;
		}
		$offsets = array_merge($idsOffsets, $valuesOffsets);

		$mo = pack('Iiiiiii', 0x950412de, 0, $items, 28, 28 + $items * 8, 0, 28 + $items * 16);
		foreach ($offsets as $offset)
			$mo .= pack('i', $offset);
		file_put_contents($file, $mo . $ids . $strings);
	}

	/**
	 * Generate gettext metadata array
	 *
	 * @return array
	 */
	private function generateMetadata($metadata)
	{
		$result = array();
		if (isset($metadata['Project-Id-Version']))
			$result[] = "Project-Id-Version: " . $metadata['Project-Id-Version'];
		else
			$result[] = "Project-Id-Version: ";
		if (isset($metadata['Report-Msgid-Bugs-To']))
			$result[] = "Report-Msgid-Bugs-To: " . $metadata['Report-Msgid-Bugs-To'];
		if (isset($metadata['POT-Creation-Date']))
			$result[] = "POT-Creation-Date: " . $metadata['POT-Creation-Date'];
		else
			$result[] = "POT-Creation-Date: ";
		$result[] = "PO-Revision-Date: " . date("Y-m-d H:iO");
		if (isset($metadata['Last-Translator']))
			$result[] = "Language-Team: " . $metadata['Language-Team'];
		else
			$result[] = "Language-Team: ";
		if (isset($metadata['MIME-Version']))
			$result[] = "MIME-Version: " . $metadata['MIME-Version'];
		else
			$result[] = "MIME-Version: 1.0";
		if (isset($metadata['Content-Type']))
			$result[] = "Content-Type: " . $metadata['Content-Type'];
		else
			$result[] = "Content-Type: text/plain; charset=UTF-8";
		if (isset($metadata['Content-Transfer-Encoding']))
			$result[] = "Content-Transfer-Encoding: " . $metadata['Content-Transfer-Encoding'];
		else
			$result[] = "Content-Transfer-Encoding: 8bit";
		if (isset($metadata['Plural-Forms']))
			$result[] = "Plural-Forms: " . $metadata['Plural-Forms'];
		else
			$result[] = "Plural-Forms: ";
		if (isset($metadata['X-Poedit-Language']))
			$result[] = "X-Poedit-Language: " . $metadata['X-Poedit-Language'];
		if (isset($metadata['X-Poedit-Country']))
			$result[] = "X-Poedit-Country: " . $metadata['X-Poedit-Country'];
		if (isset($metadata['X-Poedit-SourceCharset']))
			$result[] = "X-Poedit-SourceCharset: " . $metadata['X-Poedit-SourceCharset'];
		if (isset($metadata['X-Poedit-KeywordsList']))
			$result[] = "X-Poedit-KeywordsList: " . $metadata['X-Poedit-KeywordsList'];

		return $result;
	}

	private function buildPOFile($data, $metadata, $file)
	{
		$po = "# Gettext keys exported by Translation\Tools\Compiler \n" .
				"# Created: " . date('Y-m-d H:i:s') . "\n"
				. 'msgid ""' . "\n"
				. 'msgstr ""' . "\n";
		$po .= '"' . implode('\n"' . "\n" . '"', $this->generateMetadata($metadata)) . '\n"' . "\n\n\n";
		foreach ($data as $message => $data)
		{
			$po .= 'msgid "' . str_replace(array('"', "'"), array('\"', "\\'"), $message) . '"' . "\n";
			if (is_array($data['original']) && count($data['original']) > 1)
				$po .= 'msgid_plural "' . str_replace(array('"', "'"), array('\"', "\\'"), end($data['original'])) . '"' . "\n";
			if (!is_array($data['translation']))
				$po .= 'msgstr "' . str_replace(array('"', "'"), array('\"', "\\'"), $data['translation']) . '"' . "\n";
			elseif (count($data['translation']) < 2)
				$po .= 'msgstr "' . str_replace(array('"', "'"), array('\"', "\\'"), current($data['translation'])) . '"' . "\n";
			else
			{
				$i = 0;
				foreach ($data['translation'] as $string)
				{
					$po .= 'msgstr[' . $i . '] "' . str_replace(array('"', "'"), array('\"', "\\'"), $string) . '"' . "\n";
					$i++;
				}
			}
			$po .= "\n";
		}

		file_put_contents($file, $po);
	}

}
