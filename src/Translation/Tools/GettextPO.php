<?php
namespace Translation\Parsers;
/**
 * GettextParser
 *
 * @author Martin Bažík
 */
class GettextPO
{

	/**
	 *
	 * @param string $filename
	 * @return GettextResponse
	 */
	public static function parsePO($filename)
	{
		$messages = array();
		$metadata = array();
		$file = array();
		$lineSize = 4096;
		if(!file_exists($filename))
			throw new IOException(sprintf(__('File "%s" is not a valid file.'), $filename));
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
						$messages[$msgId] = $msgStr;
				}
			}
		}
		ksort($messages);
		return new GettextResponse($metadata, $messages);
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
		return (substr($line, 7));
	}

	private static function deleteQuotes($string)
	{
		//replace the first quote
		$string = substr_replace($string, '', 0, 1);
		//replace the second quote
		$string = substr_replace($string, '', strlen($string) - 2, 1);
		//normalize string
		$string = String::normalize($string);
		//fix encoding
		$string = String::fixEncoding($string);
		return $string;
	}

	private static function fixMetaQuotes($string)
	{
		$string = substr_replace($string, '', 0, 1);
		$string = String::normalize($string);
		return $string;
	}

	private static function fixMetaValue($string)
	{
		$string = substr_replace($string, '', 0, 1);
		//remove \n
		$string = str_replace('\n', '', $string);
		//replace the second quote
		$string = substr_replace($string, '', strlen($string) - 1, 1);
		$string = String::normalize($string);
		$string = substr_replace($string, '', strlen($string) - 1, 1);
		$string = String::normalize($string);
		return $string;
	}

}