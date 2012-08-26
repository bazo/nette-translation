<?php
namespace Mazagran\Translation;
use Mazagran\Translation\Extraction\Context;
use Nette\Utils\Neon;
/**
 * Gettext
 *
 * @author martin.bazik
 */
class Uploader
{
	private
		$endpoint = 'http://mazagran.local/api/translations'
	;
	
	public function upload($data)
	{
		$response = $this->makeRequest($data);
	}
	
	protected function makeRequest($data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->endpoint);
		curl_setopt($ch, CURLOPT_PUT, true);
		cur_setopt($ch, CURLOPT_POSTFIELDS, json_encode('data'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	
}