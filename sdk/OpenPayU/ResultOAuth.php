<?php

/**
 *	OpenPayU Standard Library
 *
 *	@package	OpenPayU
 *	@copyright	Copyright (c) 2011-2012, PayU
 *	@license	http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 */

class OpenPayU_ResultOAuth {
	private $url = '';
	private $code = '';
	private $accessToken = '';
	private $payuUserEmail = '';
	private $payuUserId = '';
	private $expiresIn = '';
	private $refreshToken = '';
	private $success = '';
	private $error = '';
	
	public function getUrl() {
		return $this->url;
	}

	public function setUrl($value) {
		$this->url = $value;
	}

	public function getCode() {
		return $this->code;
	}

	public function setCode($value) {
		$this->code = $value;
	}

	public function getAccessToken() {
		return $this->accessToken;
	}

	public function setAccessToken($value) {
		$this->accessToken = $value;
	}

	public function getPayuUserEmail() {
		return $this->payuUserEmail;
	}

	public function setPayuUserEmail($value) {
		$this->payuUserEmail = $value;
	}

	public function getPayuUserId() {
		return $this->payuUserId;
	}

	public function setPayuUserId($value) {
		$this->payuUserId = $value;
	}

	public function getExpiresIn() {
		return $this->expiresIn;
	}

	public function setExpiresIn($value) {
		$this->expiresIn = $value;
	}

	public function getRefreshToken() {
		return $this->refreshToken;
	}

	public function setRefreshToken($value) {
		$this->refreshToken = $value;
	}

	public function getSuccess() {
		return $this->success;
	}

	public function setSuccess($value) {
		$this->success = $value;
	}

	public function getError() {
		return $this->error;
	}	

	public function setError($value) {
		$this->error = $value;
	}	

}

?>
