<?php

namespace Bazo\Translation;


/**
 * Uploader
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Uploader
{

	private $endpoint;
	private $id;
	private $key;

	function __construct($endpoint, $id, $key)
	{
		$this->endpoint	 = $endpoint . '/api.projects/update/%s';
		$this->id		 = $id;
		$this->key		 = $key;
	}


	public function upload($messageData)
	{
		$data	 = serialize($messageData);
		$hash	 = hash_hmac('sha256', $data, $this->key);

		return $this->makeRequest($data, $hash);
	}


	protected function makeRequest($data, $hash)
	{
		$url = sprintf($this->endpoint, $this->id) . '?hash=' . $hash;

		$ch			 = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response	 = curl_exec($ch);
		curl_close($ch);
		return $response;
	}


}
