<?php

namespace Bazo\Translation;


/**
 * Uploader
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Downloader
{

	private $endpoint;
	private $id;
	private $key;

	function __construct($endpoint, $id, $key)
	{
		$this->endpoint	 = $endpoint . '/api.projects/download-translations/%s';
		$this->id		 = $id;
		$this->key		 = $key;
	}


	public function download()
	{
		$hash	 = hash_hmac('sha256', $this->id, $this->key);

		return $this->makeRequest($hash);
	}


	protected function makeRequest($hash)
	{
		$url = sprintf($this->endpoint, $this->id) . '?hash=' . $hash;

		$ch			 = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response	 = curl_exec($ch);
		curl_close($ch);
		return $response;
	}


}
