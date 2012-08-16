<?php
namespace Translation\Parsers;

/**
 * POParser
 *
 * @author Martin Bažík
 */
class POParser implements Parser
{

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
		$fc = str_replace(array(
			"\r\n",
			"\r"
				), array(
			"\n",
			"\n"
				), $fc);

		// results array
		$hash = array();
		// temporary array
		$temp = array();
		// state
		$state = null;
		$fuzzy = false;

		// iterate over lines
		foreach(explode("\n", $fc) as $line)
		{
			$line = trim($line);
			if($line === '')
				continue;

			list ($key, $data) = explode(' ', $line, 2);

			switch($key)
			{
				case '#,' : // flag...
					$fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data));
				case '#' : // translator-comments
				case '#.' : // extracted-comments
				case '#:' : // reference...
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
				case 'msgctxt' :
				// context
				case 'msgid' :
				// untranslated-string
				case 'msgid_plural' :
					// untranslated-string-plural
					$state = $key;
					$temp[$state] = $data;
					break;
				case 'msgstr' :
					// translated-string
					$state = 'msgstr';
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
			$hash[] = $temp;

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
			
			/**
			 * added by me
			 */
			
			$newEntry = $this->convertEntry($entry);
			
			$hash[$entry['msgid']] = $entry;
		}

		return $hash;
	}

	private function convertEntry($entry)
	{
		$newEntry = array(
			'files' => array()
		);
		
		//if()
	}
	
}
