<?php
namespace Translation\Parsers;
use Nette\Utils\Strings;
/**
 * POParser
 *
 * @author Martin Bažík
 */
class POParser implements Parser
{

	private
		$metadata
	;

	public function getMetadata()
	{
		return $this->metadata;
	}

	private function cleanHelper($x)
	{
		if(is_array($x))
		{
			foreach($x as $k => $v)
			{
				$x[$k] = $this->cleanHelper($v);
			}
		}
		else
		{
			if($x[0] == '"')
			{
				$x = substr($x, 1, -1);
			}
			$x = str_replace("\"\n\"", '', $x);
			$x = str_replace('$', '\\$', $x);
			$x = @ eval("return \"$x\";");
		}
		return $x;
	}

	/* Parse gettext .po files. */
	/* @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files */

	public function parse($file)
	{
		// read .po file
		$fc = file_get_contents($file);
		// normalize newlines
		$fc = Strings::normalize($fc);
		
		// results array
		$hash = array();
		// temporary array
		$temp = array();
		// state
		$state = null;
		$fuzzy = false;

		// iterate over lines
		$lines = explode("\n", $fc); 
		
		$comments = array();
		$metadata = array();
		
		foreach($lines as $line)
		{
			$line = Strings::trim($line);
			
			if($line === '')
			{
				$hash[] = $temp;
				$temp = array();
				$state = null;
				$fuzzy = false;
				continue;
			}

			if($this->isMetadata($line))
			{
				$line = str_replace(array('\n', '"'), '', $line);
				$meta = self::extractMetadata($line);
			    $metadata[$meta['property']] = $meta['value'];
			}
			
			list ($key, $data) = explode(' ', $line, 2);
			switch($key)
			{
				case '#:' : // reference...
					$temp['files'][] = $data;
				case '#,' : // flag...
					$fuzzy = /*$data === 'fuzzy' ? true : false;*/in_array('fuzzy', preg_split('/,\s*/', $data));
				case '#' : // translator-comments
					$comments[] = $data;
				case '#.' : // extracted-comments
				case '#|' : // msgid previous-untranslated-string
					// start a new entry
					if(sizeof($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp))
					{
						if(!$fuzzy)
							$hash[] = $temp;
						$temp = array();
						$state = null;
						$fuzzy = false;
					}
					break;
				case 'msgctxt' : // context
					$temp[$key] = $data;
					$state = $key;
					break;
				case 'msgid' : // untranslated-string
					$state = $key;
					$temp[$key] = $data;
					break;
				case 'msgid_plural' : // untranslated-string-plural
					$state = $key;
					$temp[$key] = $data;
					break;
				case 'msgstr' : // translated-string
					$state = $key;
					$temp[$state][] = $data;
					break;
				default :
					if(strpos($key, 'msgstr[') !== FALSE)
					{
						// translated-string-case-n
						$state = 'msgstr';
						$temp[$state][] = $data;
					}
					else
					{
						// continued lines
						switch($state)
						{
							case 'msgctxt' :
							case 'msgid' :
							case 'msgid_plural' :
								$temp[$state] .= "\n" . $line;
								break;
							case 'msgstr' :
								$temp[$state][sizeof($temp[$state]) - 1] .= "\n" . $line;
								break;
							default :
								// parse error
								return FALSE;
						}
					}
					break;
			}
		}

		// add final entry
		if($state == 'msgstr')
		{
			$hash[] = $temp;
		}
		

		// Cleanup data, merge multiline entries, reindex hash for ksort
		$temp = $hash;
		$hash = array();
		foreach($temp as $entry)
		{
			foreach($entry as & $v)
			{
				$v = $this->cleanHelper($v);
				if($v === FALSE)
				{
					// parse error
					return FALSE;
				}
			}
			
			$hash[$entry['msgid']] = $entry;
		}
		$this->metadata = $metadata;
		return $hash;
	}

	private function isMetadata($line)
    {
		if(substr($line, 0, 1) == '"') return true;
		else return false;
    }
	
	private function extractMetadata($line)
    {
		$meta = array();
		$parts = explode(':', $line, 2 );
		$meta['property'] = Strings::trim($parts[0]);
		$meta['value'] = Strings::trim($parts[1]);
		return $meta;
    }
	
}
