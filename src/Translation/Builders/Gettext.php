<?php
namespace Translation\Builders;
use Translation\Extraction\Context;
/**
 * Gettext
 *
 * @author martin.bazik
 */
class Gettext
{
	const OUTPUT_PO = 'PO';
	const OUTPUT_POT = 'POT';
	
	private
		/** @var array */
		$comments = array('Keys exported by Extractor'),
			
		$meta = array(
			'Content-Type' => 'text/plain; charset=UTF-8',
			'X-Poedit-SourceCharset' => 'UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		)
	;
	
	/**
	 * Gets a value of a meta key
	 *
	 * @param string $key
	 */
	public function getMeta($key)
	{
		return isset($this->meta[$key]) ? $this->meta[$key] : NULL;
	}

	/**
	 * Sets a value of a meta key
	 *
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function setMeta($key, $value)
	{
		$this->meta[$key] = $value;
		return $this;
	}
	
	/**
	 * Adds a comment to the top of the output file
	 *
	 * @param string $value
	 * @return self
	 */
	public function addComment($value)
	{
		$this->comments[] = $value;
		return $this;
	}
	
	private function formatData($data)
	{
		$output = array();
		foreach($this->comments as $comment)
		{
			$output[] = '# ' . $comment;
		}
		$output[] = '#, fuzzy';
		$output[] = 'msgid ""';
		$output[] = 'msgstr ""';
		$output[] = '"POT-Creation-Date: ' . date('c') . '\n"';
		foreach($this->meta as $key => $value)
		{
			$output[] = '"' . $key . ': ' . $value . '\n"';
		}
		$output[] = '';

		foreach($data as $message)
		{
			if(isset($message['files']))
			{
				foreach($message['files'] as $file)
				{
					$output[] = '#: ' . $file[Context::FILE] . ':' . $file[Context::LINE];
				}
			}
			if(isset($message[Context::CONTEXT]))
			{
				$output[] = $this->formatMessage($message[Context::CONTEXT], "msgctxt");
			}
			$output[] = $this->formatMessage($message[Context::SINGULAR], 'msgid');
			if(isset($message[Context::PLURAL]))
			{
				$output[] = $this->formatMessage($message[Context::PLURAL], 'msgid_plural');
				switch($this->outputMode)
				{
					case self::OUTPUT_POT:
						$output[] = 'msgstr[0] ""';
						$output[] = 'msgstr[1] ""';
						break;
					case self::OUTPUT_PO:
					// fallthrough
					default:
						$output[] = $this->formatMessage($message[Context::SINGULAR], 'msgstr[0]');
						$output[] = $this->formatMessage($message[Context::PLURAL], 'msgstr[1]');
				}
			}
			else
			{
				switch($this->outputMode)
				{
					case self::OUTPUT_POT:
						$output[] = 'msgstr ""';
						break;
					case self::OUTPUT_PO:
					// fallthrough
					default:
						$output[] = $this->formatMessage($message[Context::SINGULAR], 'msgstr');
				}
			}

			$output[] = '';
		}

		return join("\n", $output);
	}

	/**
	 * Escape a sring not to break the gettext syntax
	 *
	 * @param string $string
	 * @return string
	 */
	protected function addSlashes($string)
	{
		return addcslashes($string, Context::ESCAPE_CHARS);
	}
	
	protected function formatMessage($message, $prefix = null)
	{
		$message = $this->addSlashes($message);
		$message = '"' . str_replace("\n", "\\n\"\n\"", $message) . '"';
		return ($prefix ? $prefix . ' ' : '') . $message;
	}
	
	private function save($outputFile, $data = null)
	{
		// Output file permission check
		if(file_exists($outputFile) && !is_writable($outputFile))
		{
			$this->throwException('ERROR: Output file is not writable!');
		}

		$handle = fopen($outputFile, "w");

		fwrite($handle, $data);

		fclose($handle);

		return $this;
	}
	
	public function buildPot($outputFile, $data = null)
	{
		$this->outputMode = self::OUTPUT_POT;
		$data = $this->formatData($data);
		$this->save($outputFile, $data);
	}
	
	public function buildPo($outputFile, $data = null)
	{
		$this->outputMode = self::OUTPUT_PO;
		$data = $this->formatData($data);
		$this->save($outputFile, $data);
	}
}