<?php

namespace Translation\Parsers;

use Nette\Utils\Strings;

/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class Gettext implements Parser
{

	private
		$metadata,
			
		$dictionary
	;

	public function getMetadata()
	{
		if($this->metadata === null)
		{
			return array();
		}
		return $this->metadata;
	}

	public function parse($file)
	{
		return $this->parseMo($file);
	}
	
	public function parseMo($file)
	{
		$f = @fopen($file, 'rb');
		if(@filesize($file) < 10)
			throw new \InvalidArgumentException("'$file' is not a gettext file.");

		$endian = FALSE;
		$read = function($bytes) use ($f, $endian){
					$data = fread($f, 4 * $bytes);
					return $endian === FALSE ? unpack('V' . $bytes, $data) : unpack('N' . $bytes, $data);
				};

		$input = $read(1);
		if(Strings::lower(substr(dechex($input[1]), -8)) == "950412de")
			$endian = FALSE;
		elseif(Strings::lower(substr(dechex($input[1]), -8)) == "de120495")
			$endian = TRUE;
		else
			throw new \InvalidArgumentException("'$file' is not a gettext file.");

		$input = $read(1);

		$input = $read(1);
		$total = $input[1];

		$input = $read(1);
		$originalOffset = $input[1];

		$input = $read(1);
		$translationOffset = $input[1];

		fseek($f, $originalOffset);
		$orignalTmp = $read(2 * $total);
		fseek($f, $translationOffset);
		$translationTmp = $read(2 * $total);

		for($i = 0; $i < $total; ++$i)
		{
			if($orignalTmp[$i * 2 + 1] != 0)
			{
				fseek($f, $orignalTmp[$i * 2 + 2]);
				$original = @fread($f, $orignalTmp[$i * 2 + 1]);
			} else
				$original = "";

			if($translationTmp[$i * 2 + 1] != 0)
			{
				fseek($f, $translationTmp[$i * 2 + 2]);
				$translation = fread($f, $translationTmp[$i * 2 + 1]);
				if($original === "")
				{
					$this->metadata = $this->parseMetadata($translation);
					continue;
				}

				$original = explode(Strings::chr(0x00), $original);
				$translation = explode(Strings::chr(0x00), $translation);
				$dictionary[is_array($original) ? $original[0] : $original]['original'] = $original;
				$dictionary[is_array($original) ? $original[0] : $original]['translation'] = $translation;
			}
		}
		$this->dictionary = $dictionary;
		return $dictionary;
	}

	private function parseMetadata($input)
	{
		$input = Strings::trim($input);
		$lines = explode("\n", $input);
		foreach($lines as $line)
		{
			$pattern = ': ';
			$tmp = explode($pattern, $line, 2);
			$metadata[$tmp[0]] = $tmp[1];
		}
		return $metadata;
	}

	/**
	 *
	 * @param string $filename
	 * @return GettextResponse
	 */
	public function parsePO($filename)
	{
		$parser = new POParser;
		$data = $parser->parse($filename);
		$this->metadata = $parser->getMetadata();
		return $data;
	}
}