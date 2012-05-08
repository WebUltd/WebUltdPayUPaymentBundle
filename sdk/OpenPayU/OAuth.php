<?php

/**
 *	OpenPayU Standard Library
 *
 *	@package	OpenPayU
 *	@copyright	Copyright (c) 2011-2012, PayU
 *	@license	http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 */

class OpenPayU_OAuth extends OpenPayUOAuth
{
	/**
	 * Function returns authorize by code response
	 * @access public
	 * @param string $code
	 * @param string $returnUri
	 * @param integer $debug
	 * @return OpenPayU_ResultOAuth
	 */
	public static function accessTokenByCode($code, $returnUri, $debug = TRUE) {

		$url = OpenPayU_Configuration::getServiceUrl() . 'user/oauth/authorize';

		$result = new OpenPayU_ResultOAuth();
		$result->setUrl($url);
		$result->setCode($code);

		if ($debug) {
			OpenPayU::addOutputConsole('retrieve accessToken, authorization code mode, url' , $url);
			OpenPayU::addOutputConsole('return_uri', $returnUri);
		}

		try {
			OpenPayU::setOpenPayuEndPoint($url);
			$json = OpenPayuOAuth::getAccessTokenByCode($code, OpenPayU_Configuration::getClientId(), OpenPayU_Configuration::getClientSecret(), $returnUri);

			$result->setAccessToken($json->{'access_token'});
			$result->setPayuUserEmail($json->{'payu_user_email'});
			$result->setPayuUserId($json->{'payu_user_id'});
			$result->setExpiresIn($json->{'expires_in'});
			$result->setRefreshToken($json->{'refresh_token'});
			$result->setSuccess(1);
		} catch (Exception $ex) {
			$result->setSuccess(0);
			$result->setError($ex->getMessage());
		}

		return $result;
	}

	/**
	 * Function returns authorize by client credentials response
	 * @access public
	 * @param integer $debug
	 * @return OpenPayU_ResultOAuth
	 */
	public static function accessTokenByClientCredentials($debug = TRUE) {

		$url = OpenPayU_Configuration::getServiceUrl() . 'oauth/authorize';

		$result = new OpenPayU_ResultOAuth();
		$result->setUrl($url);

		OpenPayU::setOpenPayuEndPoint($url);
		if ($debug) {
			OpenPayU::addOutputConsole('retrieve accessToken', 'retrieve accessToken, client credentials mode, url: ' . $url);
		}

		try {
			OpenPayU::setOpenPayuEndPoint($url);
			$json = OpenPayUOAuth::getAccessTokenByClientCredentials(OpenPayU_Configuration::getClientId(), OpenPayU_Configuration::getClientSecret());

			$result->setAccessToken($json->{'access_token'});
			$result->setPayuUserEmail($json->{'payu_user_email'});
			$result->setPayuUserId($json->{'payu_user_id'});
			$result->setExpiresIn($json->{'expires_in'});
			$result->setRefreshToken($json->{'refresh_token'});
			$result->setSuccess(1);
		} catch (Exception $ex) {
			$result->setSuccess(0);
			$result->setError($ex->getMessage());
		}

		return $result;
	}
}

?>
