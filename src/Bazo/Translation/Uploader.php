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
		$this->endpoint = $endpoint;
		$this->id = $id;
		$this->key = $key;
	}


	public function upload($messageData)
	{
		$messageData = serialize($messageData);
		$hash = hash_hmac('sha256', $messageData, $this->key);

		$data = [
			'messageData' => $messageData,
			'hash' => $hash
		];

		return $this->makeRequest($data);
	}


	protected function makeRequest($data)
	{
		$query = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, sprintf($this->endpoint, $this->id));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_encode($response);
	}


}
