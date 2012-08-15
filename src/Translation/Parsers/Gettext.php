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
		$metadata
	;

	public function getMetadata()
	{
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
					$this->parseMetadata($translation);
					continue;
				}

				$original = explode(Strings::chr(0x00), $original);
				$translation = explode(Strings::chr(0x00), $translation);
				$dictionary[is_array($original) ? $original[0] : $original]['original'] = $original;
				$dictionary[is_array($original) ? $original[0] : $original]['translation'] = $translation;
			}
		}
		return $dictionary;
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

	/**
	 *
	 * @param string $filename
	 * @return GettextResponse
	 */
	public function parsePO($filename)
	{
		$messages = array();
		$metadata = array();
		$file = array();
		$lineSize = 4096;
		if(!file_exists($filename))
		{
			throw new IOException(sprintf(__('File "%s" is not a valid file.'), $filename));
		}
		else //parse file, extract metadata and extract message information
		{
			$handle = fopen($filename, 'r');
			if($handle)
			{
				while(($line = fgets($handle, $lineSize)) !== false)
				{
					if(!self::isComment($line))
					{
						if(self::isMetadata($line))
						{
							$meta = self::extractMetadata($line);
							$property = self::fixMetaQuotes($meta['property']);
							$value = self::fixMetaValue($meta['value']);
							$metadata[$property] = $value;
						}
						else
						{
							if(!self::isEmptyLine($line))
							{
								$file[] = $line;
							}
						}
					}
				}
				if(!feof($handle))
				{
					throw new IOException(sprintf(__('Error: unexpected fgets() fail in "%s".'), $filename));
					echo "Error: unexpected fgets() fail\n";
				}
				fclose($handle);
			}
		}
		//generate message pairs
		foreach($file as $lineNo => $line)
		{
			if(self::isMsgId($line))
			{
				if(@self::isMsgStr($file[$lineNo + 1]))
				{
					$msgId = self::deleteQuotes(self::extractMsgId($line));
					$msgStr = self::deleteQuotes(self::extractMsgStr($file[$lineNo + 1]));
					if($msgId != '')
					{
						$messages[$msgId] = $msgStr;
					}
				}
			}
		}
		
		$dictionary = array();
		
		foreach($messages as $phrase => $translation)
		{
		    $dictionary[$phrase] = array(
			'original' => array(
			    0 => $phrase
			),
			'translation' => array(
			    0 => $translation
			)
		    );
		}
		
		ksort($dictionary);
		$this->metadata = $metadata;
		return $dictionary;
	}

	private static function isComment($line)
	{
		if(substr($line, 0, 1) == '#')
			return true;
		else
			return false;
	}

	private static function isMetadata($line)
	{
		if(substr($line, 0, 1) == '"')
			return true;
		else
			return false;
	}

	private static function extractMetadata($line)
	{
		$meta = array();
		$parts = explode(':', $line, 2);
		$meta['property'] = $parts[0];
		$meta['value'] = $parts[1];
		return $meta;
	}

	private static function isEmptyLine($line)
	{
		if(strlen($line) == 1)
			return true;
		else
			return false;
	}

	private static function isMsgId($line)
	{
		if(substr($line, 0, 5) == 'msgid')
			return true;
		else
			return false;
	}

	private static function isMsgStr($line)
	{
		if(substr($line, 0, 6) == 'msgstr')
			return true;
		else
			return false;
	}

	private static function extractMsgId($line)
	{
		return (substr($line, 6));
	}

	private static function extractMsgStr($line)
	{
		return substr($line, 7);
	}

	private static function deleteQuotes($string)
	{
		//replace the first quote
		$string = substr_replace($string, '', 0, 1);
		//replace the second quote
		$string = substr_replace($string, '', strlen($string) - 2, 1);
		//normalize string
		$string = Strings::normalize($string);
		//fix encoding
		$string = Strings::fixEncoding($string);
		return $string;
	}

	private static function fixMetaQuotes($string)
	{
		$string = substr_replace($string, '', 0, 1);
		$string = Strings::normalize($string);
		return $string;
	}

	private static function fixMetaValue($string)
	{
		$string = substr_replace($string, '', 0, 1);
		//remove \n
		$string = str_replace('\n', '', $string);
		//replace the second quote
		$string = substr_replace($string, '', strlen($string) - 1, 1);
		$string = Strings::normalize($string);
		$string = substr_replace($string, '', strlen($string) - 1, 1);
		$string = Strings::normalize($string);
		return $string;
	}

}