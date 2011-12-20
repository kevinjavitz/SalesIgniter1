<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

class CurlRequest
{

	/**
	 * @var array
	 */
	private $options = array();

	/**
	 * @var null|array|string
	 */
	private $dataRaw = null;

	/**
	 * @var null|string
	 */
	private $dataFormatted = null;

	/**
	 * @var array
	 */
	private $httpHeaders = array();

	public function __construct($url = null) {
		if (!function_exists('curl_init')) {
			trigger_error('The cURL extension is not installed.');
		}

		$this->requestObj = curl_init();

		if (is_null($url) === false){
			$this->setUrl($url);
		}
		$this->setHeader(false);
		$this->setReturnTransfer(true);
		$this->setSendMethod('post');
		$this->setOption(CURLOPT_VERBOSE, true);
		$this->setOption(CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
	}

	/**
	 * @param string $method
	 */
	public function setSendMethod($method = 'get') {
		if ($method == 'post'){
			$this->setOption(CURLOPT_POST, true);
		}
		else {
			$this->setOption(CURLOPT_POST, false);
		}
	}

	/**
	 * @param string $format
	 * @return array|null|string
	 */
	public function formatData($format = 'url') {
		$data = $this->getDataRaw();
		if (is_array($data)){
			$dataStr = array();
			if ($format == 'url'){
				foreach($data as $k => $v){
					$dataStr[] = $k . '=' . urlencode(str_replace(',', '', $v));
				}
				$dataStr = implode('&', $dataStr);
			}
			elseif ($format == 'xml') {
				$dataStr[] = '<?xml version="1.0"?>';
				foreach($data as $root => $tags){
					$dataStr[] = '<' . $root . '>';
					foreach($tags as $k => $v){
						$dataStr[] = '<' . $k . '>' . $v . '</' . $k . '>';
					}
					$dataStr[] = '</' . $root . '>';
				}
				$dataStr = implode('', $dataStr);
			}
		}
		else {
			$dataStr = $data;
		}
		return $dataStr;
	}

	/**
	 * @param $data
	 * @param string $format
	 * @param string $dataBefore
	 * @param string $dataAfter
	 */
	public function setData($data, $format = 'url', $dataBefore = '', $dataAfter = '') {
		$this->setDataRaw($data);
		$this->setDataFormatted($dataBefore . $this->formatData($format) . $dataAfter);
		if (isset($this->options[CURLOPT_POST]) && $this->options[CURLOPT_POST] === true){
			$this->setOption(CURLOPT_POSTFIELDS, $this->getDataFormatted());
		}
	}

	/**
	 * @param $val
	 */
	public function setDataRaw($val) {
		$this->dataRaw = $val;
	}

	/**
	 * @param $val
	 */
	public function setDataFormatted($val) {
		$this->dataFormatted = $val;
	}

	/**
	 * @return array|null|string
	 */
	public function getDataRaw() {
		return $this->dataRaw;
	}

	/**
	 * @return null|string
	 */
	public function getDataFormatted() {
		return $this->dataFormatted;
	}

	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->options[CURLOPT_URL];
	}

	/**
	 * @param $url
	 */
	public function setUrl($url) {
		$this->setOption(CURLOPT_URL, $url);
	}

	/**
	 * @param $val
	 */
	public function setHeader($val) {
		$this->setOption(CURLOPT_HEADER, $val);
	}

	/**
	 * @param string $k
	 * @param string $v
	 */
	public function setHttpHeader($k, $v){
		$this->httpHeaders[$k] = $v;
	}

	/**
	 * @param $val
	 */
	public function setReturnTransfer($val) {
		$this->setOption(CURLOPT_RETURNTRANSFER, $val);
	}

	/**
	 * @param $username
	 * @param $password
	 */
	public function setLoginInfo($username, $password) {
		$this->setOption(CURLOPT_USERPWD, $username . ':' . $password);
		$this->setOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	}

	/**
	 *
	 */
	public function close() {
		curl_close($this->requestObj);
	}

	/**
	 * @param $optionName
	 * @param $optionValue
	 */
	public function setOption($optionName, $optionValue) {
		if (!@curl_setopt($this->requestObj, $optionName, $optionValue)){
			die($optionName . '::' . $optionValue);
		}

		$this->options[$optionName] = $optionValue;
	}

	/**
	 * @return CurlResponse
	 */
	public function execute() {
		if (!empty($this->httpHeaders)){
			$headers = array();
			foreach($this->httpHeaders as $k => $v){
				$headers[] = $k . ': ' . $v;
			}
			$this->setOption(CURLOPT_HTTPHEADER, $headers);
		}

		if ($this->options[CURLOPT_POST] === false){
			$url = $this->getUrl();
			$this->setUrl($url . (stristr($url, '?') ? '&' : '?') . $this->getDataFormatted());
		}
		$Response = new CurlResponse($this);
		return $Response;
	}
}

?>