<?php
namespace Translation\Extraction;

/**
 * Context
 *
 * @author martin.bazik
 */
class Context
{
	const ESCAPE_CHARS = '"';
	const OUTPUT_PO = 'PO';
	const OUTPUT_POT = 'POT';
	const CONTEXT = 'msgctxt';//'context';
	const SINGULAR = 'msgid';//'singular';
	const PLURAL = 'msgid_plural';//'plural';
	const LINE = 'line';
	const FILE = 'file';
	const TRANSLATION = 'msgstr';
}