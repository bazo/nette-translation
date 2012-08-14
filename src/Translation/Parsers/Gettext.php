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
		$dictionary,
			
		$metadata
	;
	
	public function getDictionary()
	{
		return $this->dictionary;
	}

	public function getMetadata()
	{
		return $this->metadata;
	}

	public function parse($file)
	{
		$f = @fopen($file, 'rb');
		if(@filesize($file) < 10)
			\InvalidArgumentException("'$file' is not a gettext file.");

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
					$this->parseMetadata($translation);
					continue;
				}

				$original = explode(Strings::chr(0x00), $original);
				$translation = explode(Strings::chr(0x00), $translation);
				$this->dictionary[is_array($original) ? $original[0] : $original]['original'] = $original;
				$this->dictionary[is_array($original) ? $original[0] : $original]['translation'] = $translation;
			}
		}
		return $this->dictionary;
	}
	
	private function parseMetadata($input)
	{
		$input = trim($input);

		$input = preg_split('/[\n,]+/', $input);
		foreach($input as $metadata)
		{
			$pattern = ': ';
			$tmp = preg_split("($pattern)", $metadata);
			$this->metadata[trim($tmp[0])] = count($tmp) > 2 ? ltrim(strstr($metadata, $pattern), $pattern) : $tmp[1];
		}
	}

}