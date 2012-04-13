<?php

/*
	ver. 0.1.1
	OpenPayU Standard Library

	Copyright (c) 2011-2012 PayU
	http://www.payu.com

	CHANGE_LOG:
		2011-09-09, ver. 0.0.15
		- added http header authentication
		2011-11-04, ver. 0.0.16
		- changes connected with changing OrderUpdateRequest with OrderNotifyRequest.
		2011-11-06, ver. 0.0.17
		- transfer of algorithm computing authentication header to SKD
		2011-11-07, ver. 0.0.18
		- bugfix for document parsing errors
		2011-12-20, ver. 0.1.0
		- added classes OpenPayU_Configuration, OpenPayU_Order, OpenPayU_OAuth
		- added metod verifyResponse
		2012-01-03, ver. 0.1.1
		- arguments in function environment is converted to lower char
*/

include_once("openpayu_domain.php");

class OpenPayUNetwork {

	protected static $openPayuEndPointUrl = '';

	public static function setOpenPayuEndPoint($ep) {
		OpenPayUNetwork::$openPayuEndPointUrl = $ep;
		return;
	}

	private static function isCurlInstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}

	public static function getOpenPayuEndPoint() {
		if (OpenPayUNetwork::$openPayuEndPointUrl == "") {
			throw new Exception("OpenPayUNetwork::$openPayuEndPointUrl is empty");
		}

		return OpenPayUNetwork::$openPayuEndPointUrl;
	}

	public static function sendOpenPayuDocument($doc) {

		if (OpenPayUNetwork::$openPayuEndPointUrl == "") {
			throw new Exception("OpenPayUNetwork::$openPayuEndPointUrl is empty");
		}

		$response = '';
		$xml = urlencode($doc);
		if (OpenPayUNetwork::isCurlInstalled()) {
			$response = OpenPayU::sendData( OpenPayUNetwork::$openPayuEndPointUrl, "DOCUMENT=".$xml);
		} else {
			throw new Exception("curl is not available");
		}

		return $response;
	}

	public static function sendOpenPayuDocumentAuth($doc, $merchantPosId, $signatureKey, $algorithm = "MD5") {

		if (OpenPayUNetwork::$openPayuEndPointUrl == "") {
			throw new Exception("OpenPayUNetwork::$openPayuEndPointUrl is empty");
		}

		if ( $signatureKey == null || $signatureKey == "" ) {
			throw new Exception("Merchant Signature Key should not be null or empty.");
		}

		if ( $merchantPosId== null || $merchantPosId=="" ) {
			throw new Exception("MerchantPosId should not be null or empty.");
		}
		$tosigndata = $doc.$signatureKey;
		$xml = urlencode($doc);
		$signature = "";
		if($algorithm=="MD5"){
			$signature = md5($tosigndata);
		} else if($algorithm=="SHA"){
			$signature = sha1($tosigndata);
		} else if($algorithm=="SHA-256" || $algorithm=="SHA256" || $algorithm=="SHA_256"){
			$signature = hash("sha256",$tosigndata);
		}
		$authData = 'sender='.$merchantPosId.
					';signature='.$signature.
					';algorithm='.$algorithm.
					';content=DOCUMENT';
		$response = '';

		if (OpenPayUNetwork::isCurlInstalled()) {
			$response = OpenPayU::sendDataAuth( OpenPayUNetwork::$openPayuEndPointUrl, "DOCUMENT=".$xml, $authData);
		} else {
			throw new Exception("curl is not available");
		}

		return $response;
	}

	public static function sendDataAuth( $url, $doc,$authData) {

		$ch = curl_init($url );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $doc);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch,CURLOPT_HTTPHEADER,array('OpenPayu-Signature:'.$authData));

		$response = curl_exec($ch);

		return $response;
	}

	public static function sendData( $url, $doc) {

		$ch = curl_init($url );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $doc);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);

		return $response;
	}
}

class OpenPayUBase extends OpenPayUNetwork {

	protected static $outputConsole = '';

	public static function printOutputConsole() {
		echo OpenPayU::$outputConsole;
	}

	public static function buildOpenPayURequestDocument($data, $startElement, $version = '1.0', $xml_encoding = 'UTF-8') {
		return OpenPayUBase::buildOpenPayUDocument($data, $startElement, 1, $version, $xml_encoding);
	}

	public static function buildOpenPayUResponseDocument($data, $startElement, $version = '1.0', $xml_encoding = 'UTF-8') {
		return OpenPayUBase::buildOpenPayUDocument($data, $startElement, 0, $version, $xml_encoding);
	}

	public static function arr2xml(XMLWriter $xml, $data, $parent) {
		foreach($data as $key => $value) {
			if (is_array($value)){
				if (is_numeric($key)) {
					OpenPayUBase::arr2xml($xml, $value, $key);
				} else {
					$xml->startElement($key);
					OpenPayUBase::arr2xml($xml, $value, $key);
					$xml->endElement();
				}
			continue;
			}
		$xml->writeElement($key, $value);
		}
	}

	public static function arr2form($data, $parent, $index) {
		$fragment = '';
		foreach($data as $key => $value) {
			if (is_array($value)){
				if (is_numeric($key)) {
					$fragment .= OpenPayUBase::arr2form($value, $parent, $key);
				} else {
					$p = $parent != "" ? $parent . "." . $key : $key;
					if (is_numeric($index)) {
						$p .= "[" . $index . "]";
					}
					$fragment .= OpenPayUBase::arr2form($value, $p, $key);
				}
			continue;
			}

		$path = $parent != "" ? $parent . "." . $key : $key;
		$fragment .= OpenPayUBase::buildFormFragmentInput($path, $value);
		}

		return $fragment;
	}

	public static function read($xml) {
		$tree = null;
		while($xml->read()) {
			if($xml->nodeType == XMLReader::END_ELEMENT) {
				return $tree;
			}

			else if($xml->nodeType == XMLReader::ELEMENT) {
				if (!$xml->isEmptyElement)	{
					$tree[$xml->name] = OpenPayUBase::read($xml);
				}
			}

			else if($xml->nodeType == XMLReader::TEXT) {
				$tree = $xml->value;
			}
		}
		return $tree;
	}

	public static function buildOpenPayUDocument($data, $startElement, $request = 1, $xml_version = '1.0', $xml_encoding = 'UTF-8') {
		if(!is_array($data)){
			return false;
		}

		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument($xml_version, $xml_encoding);
		$xml->startElementNS(null, 'OpenPayU', 'http://www.openpayu.com/openpayu.xsd');

			$header = $request == 1 ? "HeaderRequest" : "HeaderResponse";

			$xml->startElement($header);

				$xml->writeElement('Algorithm', 'MD5');

				$xml->writeElement('SenderName', 'exampleSenderName');
				$xml->writeElement('Version', $xml_version);

			$xml->endElement();

			// domain level - open
			$xml->startElement(OpenPayUDomain::getDomain4Message($startElement));

				// message level - open
				$xml->startElement($startElement);

				OpenPayUBase::arr2xml($xml, $data, $startElement);

				// message level - close
				$xml->endElement();
			// domain level - close
			$xml->endElement();
		// document level - close
		$xml->endElement();

		return $xml->outputMemory(true);
	}

	public static function buildFormFragmentInput($name, $value, $type = "hidden") {
		return "<input type='$type' name='$name' value='$value'>\n";
	}

	public static function buildOpenPayuForm($data, $msgName, $version= "1.0", $encoding = 'UTF-8') {
		if(!is_array($data)) {
			return false;
		}

		$url = OpenPayUNetwork::getOpenPayuEndPoint();

		$form  = "<form method='post' action='" . $url . "'>\n";
		$form .= OpenPayUBase::buildFormFragmentInput("HeaderRequest.Version", $version);
		$form .= OpenPayUBase::buildFormFragmentInput("HeaderRequest.Name", $msgName);
		$form .= OpenPayUBase::arr2form($data, "", "");
		$form .= "</form>";

		return $form;
	}

	public static function parseOpenPayUDocument($xmldata) {

		$xml = new XMLReader();
		$xml->XML($xmldata);

		$assoc = OpenPayUBase::read($xml);

		return $assoc;
	}
}

class OpenPayU extends OpenPayUBase {

	public static function buildOrderCreateRequest($data)
	{
		$xml = OpenPayU::buildOpenPayURequestDocument($data, "OrderCreateRequest");
		return $xml;
	}

	public static function buildOrderRetrieveRequest($data)
	{
		$xml = OpenPayU::buildOpenPayURequestDocument($data, "OrderRetrieveRequest");
		return $xml;
	}

	public static function buildShippingCostRetrieveResponse($data, $reqId, $countryCode) {

		$cost = array (	'ResId' =>  $reqId,
							'Status' => array('StatusCode' => 'OPENPAYU_SUCCESS'),
							'AvailableShippingCost' => $data);

		$xml = OpenPayU::buildOpenPayUResponseDocument($cost, "ShippingCostRetrieveResponse");
		return $xml;
	}

	public static function buildOrderNotifyResponse($reqId) {

		$cost = array (
			'ResId' =>  $reqId,
			'Status' => array('StatusCode' => 'OPENPAYU_SUCCESS')
		);

		$xml = OpenPayU::buildOpenPayUResponseDocument($cost, "OrderNotifyResponse");
		return $xml;
	}

	public static function verifyResponse($data, $message) {

		$arr = OpenPayU::parseOpenPayUDocument(stripslashes($data));
		$status_code = $arr['OpenPayU']['OrderDomainResponse'][$message]['Status'];
		if($status_code == null){
			$status_code = $arr['OpenPayU']['HeaderResponse']['Status'];
		}
		return $status_code;
	}

	public static function verifyOrderCancelResponseStatus($data) {
		return verifyResponse($data, 'OrderCancelResponse');
	}

	public static function verifyOrderStatusUpdateResponseStatus($data) {
		return verifyResponse($data, 'OrderStatusUpdateResponse');
	}

	public static function verifyOrderCreateResponse($data) {

		$arr = OpenPayU::parseOpenPayUDocument(stripslashes($data));
		$status_code = $arr['OpenPayU']['OrderDomainResponse']['OrderCreateResponse']['Status'];
		if($status_code == null){
			$status_code = $arr['OpenPayU']['HeaderResponse']['Status'];
		}
		return $status_code;
	}

	public static function verifyOrderRetrieveResponseStatus($data) {

		$arr = OpenPayU::parseOpenPayUDocument(stripslashes($data));
		$status_code = $arr['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['Status'];
		if($status_code == null){
			$status_code = $arr['OpenPayU']['HeaderResponse']['Status'];
		}
		return $status_code;
	}

	public static function getOrderRetrieveResponse($data)
	{
		$arr = OpenPayU::parseOpenPayUDocument(stripslashes($data));
		$order_retrieve = $arr['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse'];

		return $order_retrieve;
	}

	public static function buildOrderCancelRequest($data) {
		$xml = OpenPayU::buildOpenPayURequestDocument($data, "OrderCancelRequest");
		return $xml;
	}

	public static function buildOrderStatusUpdateRequest($data) {
		$xml = OpenPayU::buildOpenPayURequestDocument($data, "OrderStatusUpdateRequest");
		return $xml;
	}
}

class OpenPayuOAuth extends OpenPayUBase {

	public static function getAccessTokenByCode($code, $oauth_client_name, $oauth_client_secret, $page_redirect) {
		$url = OpenPayUNetwork::$openPayuEndPointUrl;
		$params = 'code=' . $code . '&client_id=' . $oauth_client_name . '&client_secret=' . $oauth_client_secret . '&grant_type=authorization_code&redirect_uri=' . $page_redirect;
		$response = OpenPayU::sendData( OpenPayUNetwork::$openPayuEndPointUrl, $params);

		$resp_json =  json_decode($response);
		OpenPayU::$outputConsole = "<br/><b>oauth response: " . $response . "</b><br/>";
		$access_token = $resp_json->{"access_token"};

		if(empty($access_token)) {
			throw new Exception("access_token is empty, error: " . $response);
		}

		return $resp_json;
	}

	public static function getAccessToken($code, $oauth_client_name, $oauth_client_secret, $page_redirect) {
		$url = OpenPayUNetwork::$openPayuEndPointUrl;
		$params = 'code=' . $code . '&client_id=' . $oauth_client_name . '&client_secret=' . $oauth_client_secret . '&grant_type=authorization_code&redirect_uri=' . $page_redirect;

		$response = OpenPayU::sendData( OpenPayUNetwork::$openPayuEndPointUrl, $params);

		$resp_json =  json_decode($response);
		OpenPayU::$outputConsole = "<br/><b>oauth response: " . $response . "</b><br/>";
		$access_token = $resp_json->{"access_token"};

		if(empty($access_token)) {
			throw new Exception("access_token is empty, error: " . $response);
		}

		return $access_token;
	}

	public static function getAccessTokenByClientCredentials($oauth_client_name, $oauth_client_secret)
	{
		$url = OpenPayUNetwork::$openPayuEndPointUrl;
		$params = 'client_id=' . $oauth_client_name . '&client_secret=' . $oauth_client_secret . '&grant_type=client_credentials';

		$response = OpenPayU::sendData(OpenPayUNetwork::$openPayuEndPointUrl, $params);

		$resp_json =  json_decode($response);
		OpenPayU::$outputConsole = "<br/><b>oauth response: " . $response . "</b><br/>";

		$access_token = $resp_json->{"access_token"};

		if(empty($access_token)) {
			throw new Exception("access_token is empty, error: " . $response);
		}

		return $resp_json;
	}

	public static function getAccessTokenOnly($oauth_client_name, $oauth_client_secret)
	{
		$url = OpenPayUNetwork::$openPayuEndPointUrl;
		$params = 'client_id=' . $oauth_client_name . '&client_secret=' . $oauth_client_secret . '&grant_type=client_credentials';

		$response = OpenPayU::sendData(OpenPayUNetwork::$openPayuEndPointUrl, $params);

		$resp_json =  json_decode($response);
		OpenPayU::$outputConsole = "<br/><b>oauth response: " . $response . "</b><br/>";

		$access_token = $resp_json->{"access_token"};

		if(empty($access_token)) {
			throw new Exception("access_token is empty, error: " . $response);
		}

		return $access_token;
	}
}


class OpenPayU_Configuration {

	public static $env = 'sandbox';
	public static $merchantPosId = '';
	public static $posAuthKey = '';
	public static $clientId = '';
	public static $clientSecret = '';
	public static $signatureKey = '';


	public static $serviceUrl = '';
	public static $summaryUrl = '';
	public static $authUrl = '';
	public static $serviceDomain = '';

	public static function environment($value = "sandbox", $domain = 'payu.pl', $country = 'pl') {
		$value = strtolower($value);
		$domain = strtolower($domain);
		$country = strtolower($country);

		if ($value == 'sandbox' || $value == 'secure') {
			OpenPayU_Configuration::$env = $value;
			OpenPayU_Configuration::$serviceDomain = $domain;

			OpenPayU_Configuration::$serviceUrl = "https://" . $value . '.' . $domain . "/" . $country . "/standard/";
			OpenPayU_Configuration::$summaryUrl = OpenPayU_Configuration::$serviceUrl . "co/summary";
			OpenPayU_Configuration::$authUrl = OpenPayU_Configuration::$serviceUrl . "oauth/user/authorize";

			return;
		}

		throw new Exception("Invalid value:$value for environment. Proper values are: 'sandbox' or 'secure'.");
	}

	public static function merchantPosId($value) {
		OpenPayU_Configuration::$merchantPosId = $value;
	}

	public static function posAuthKey($value) {
		OpenPayU_Configuration::$posAuthKey = $value;
	}

	public static function clientId($value) {
		OpenPayU_Configuration::$clientId = $value;
	}

	public static function clientSecret($value) {
		OpenPayU_Configuration::$clientSecret = $value;
	}

	public static function signatureKey($value) {
		OpenPayU_Configuration::$signatureKey = $value;
	}

}

class OpenPayU_Result {
	public $status = '';
	public $error = '';
	public $success = 0;
	public $request = '';
	public $response = '';
	public $sessionId = '';
	public $msg = '';
}

class OpenPayU_Order extends OpenPayU {


	public static function create($order, $debug = 1) {

		/*
		openpayu data model
		OrderCreateRequest : http://www.payu.com/openpayu/OrderDomainRequest.html#Link2
		OrderCreateResponse : http://www.payu.com/openpayu/OrderDomainResponse.html#Link2
		*/

		// preparing payu service for order initialization
		$OrderCreateRequestUrl = OpenPayU_Configuration::$serviceUrl . "co/openpayu/OrderCreateRequest";
		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>OpenPayU endpoint for OrderCreateRequest message : " . $OrderCreateRequestUrl . "</b><br/>";
		}
		OpenPayU::setOpenPayuEndPoint($OrderCreateRequestUrl);

		// convert array to openpayu document
		$xml = OpenPayU::buildOrderCreateRequest($order);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCreateRequest message: </b><br/>" .  htmlentities($xml);
		}
		$merchantPosId = OpenPayU_Configuration::$merchantPosId;
		$signatureKey = OpenPayU_Configuration::$signatureKey;

		// send openpayu document with order initialization structure to PayU service
		$response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCreateResponse message: </b><br/>" . htmlentities($response);
		}

		// verify response from PayU service
		$status = OpenPayU::verifyOrderCreateResponse($response);

		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCreateResponse status: </b><br/>" . serialize($status);
		}

		$result = new OpenPayU_Result();
		$result->status = $status;
		$result->error = $status['StatusCode'];
		$result->success = ($status['StatusCode'] == "OPENPAYU_SUCCESS" ? 1 : 0);
		$result->request = $order;
		$result->response = OpenPayU::parseOpenPayUDocument($response);

		return $result;
	}

	public static function retrieve($sessionId, $debug = 1) {
		$req = array ( 	'ReqId' => md5(rand()),
						'MerchantPosId' => OpenPayU_Configuration::$merchantPosId,
						'SessionId' => $sessionId);

		$OrderRetrieveRequestUrl = OpenPayU_Configuration::$serviceUrl . "co/openpayu/OrderRetrieveRequest";
		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>OpenPayU endpoint for OrderRetrieveRequest message : " . $OrderRetrieveRequestUrl . "</b><br/>";
		}

		$oauthResult = OpenPayu_OAuth::accessTokenByClientCredentials();

		OpenPayU::setOpenPayuEndPoint($OrderRetrieveRequestUrl . "?oauth_token=" . $oauthResult->accessToken);
		$xml = OpenPayU::buildOrderRetrieveRequest($req);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderRetrieveRequest message: </b><br/>" .  htmlentities($xml);
		}

		$merchantPosId = OpenPayU_Configuration::$merchantPosId;
		$signatureKey = OpenPayU_Configuration::$signatureKey;
		$response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderRetrieveResponse message: </b><br/>" . htmlentities($response);
		}

		$status = OpenPayU::verifyOrderCreateResponse($response);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderRetrieveResponse status: </b><br/>" . serialize($status);
		}

		$result = new OpenPayU_Result();
		$result->status = $status;
		$result->error = $status['StatusCode'];
		$result->success = ($status['StatusCode'] == "OPENPAYU_SUCCESS" ? 1 : 0);
		$result->request = $order;

		try {
			$assoc = OpenPayU::parseOpenPayUDocument($response);
			$result->response = $assoc;
		} catch(Exception $ex) {
			if ($debug) {
				OpenPayU::$outputConsole .= "<br/>OrderRetrieveResponse parse result exception: " . $ex->getMessage();
			}
		}

		return $result;
	}

	public static function consumeMessage($xml, $debug = 1) {
	    $xml = stripslashes(urldecode($xml));
		$rq = OpenPayU::parseOpenPayUDocument($xml);

		$msg = $rq['OpenPayU']['OrderDomainRequest'];

		switch (key($msg)) {
			case 'OrderNotifyRequest':
				return OpenPayU_Order::consumeNotification($xml);
				break;
			case 'ShippingCostRetrieveRequest':
				return OpenPayU_Order::consumeShippingCostRetrieveRequest($xml);
				break;
			default:
				return key($smg);
				break;
		}
	}

	private static function consumeNotification($xml, $debug = 1) {

		if ($debug) {
			OpenPayU::$outputConsole = "<b>OrderNotifyRequest message: </b><br/>" . $xml;
		}

	    $xml = stripslashes(urldecode($xml));
		$rq = OpenPayU::parseOpenPayUDocument($xml);
		$reqId = $rq['OpenPayU']['OrderDomainRequest']['OrderNotifyRequest']['ReqId'];
		$sessionId = $rq['OpenPayU']['OrderDomainRequest']['OrderNotifyRequest']['SessionId'];

		if ($debug) {
			OpenPayU::$outputConsole = "OrderNotifyRequest data, reqId: " . $reqId . ", sessionId: " . $sessionId;
		}

		// response to payu service
		$rsp = OpenPayU::buildOrderNotifyResponse($reqId);
		if ($debug) {
			OpenPayU::$outputConsole = "<b>OrderNotifyResponse message: </b><br/>" . $rsp;
		}
		header("Content-Type:text/xml");
		echo $rsp;

		$result = new OpenPayU_Result();
		$result->request = $rq;
		$result->response = $rsp;
		$result->success = 1;
		$result->sessionId = $sessionId;
		$result->message = 'OrderNotifyRequest';

		// if everything is alright return full data sent from payu service to client
		return $result;
	}

	private static function consumeShippingCostRetrieveRequest($xml, $debug = 1) {
		if ($debug) {
			OpenPayU::$outputConsole = "<b>consumeShippingCostRetrieveRequest message: </b><br/>" . $xml;
		}

		$result = new OpenPayU_Result();

		$rq = OpenPayU::parseOpenPayUDocument($xml);
		$result->countryCode = $rq['OpenPayU']['OrderDomainRequest']['ShippingCostRetrieveRequest']['CountryCode'];
		$result->reqId = $rq['OpenPayU']['OrderDomainRequest']['ShippingCostRetrieveRequest']['ReqId'];
		$result->message = 'ShippingCostRetrieveRequest';

		if ($debug) {
			OpenPayU::$outputConsole = "consumeShippingCostRetrieveRequest reqId: " . $result->reqId . ", countryCode: " . $result->countryCode . "<br/>";
		}

		return $result;
	}

	public static function cancel($sessionId, $debug = 1) {

		$rq = array ( 	'ReqId' => md5(rand()),
						'MerchantPosId' => OpenPayU_Configuration::$merchantPosId,
						'SessionId' => $sessionId);

		$result = new OpenPayU_Result();
		$result->request = $rq;

		$url = OpenPayU_Configuration::$serviceUrl . "co/openpayu/OrderCancelRequest";
		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>OpenPayU endpoint for OrderCancelRequest message : " . $url . "</b><br/>";
		}

		$oauthResult = OpenPayu_OAuth::accessTokenByClientCredentials();
		OpenPayU::setOpenPayuEndPoint($url . "?oauth_token=" . $oauthResult->accessToken);

		$xml = OpenPayU::buildOrderCancelRequest($rq);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCancelRequest message: </b><br/>" .  htmlentities($xml);
		}

		$merchantPosId = OpenPayU_Configuration::$merchantPosId;
		$signatureKey = OpenPayU_Configuration::$signatureKey;
		$response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCancelResponse message: </b><br/>" . htmlentities($response);
		}

		// verify response from PayU service
		$status = OpenPayU::verifyOrderCancelResponseStatus($response);

		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderCancelResponse status: </b><br/>" . serialize($status);
		}

		$result->status = $status;
		$result->error = $status['StatusCode'];
		$result->success = ($status['StatusCode'] == "OPENPAYU_SUCCESS" ? 1 : 0);
		$result->response = OpenPayU::parseOpenPayUDocument($response);

		return $result;
	}

	public static function updateStatus($sessionId, $status, $debug = 1) {

		$rq = array ( 	'ReqId' => md5(rand()),
						'MerchantPosId' => OpenPayU_Configuration::$merchantPosId,
						'SessionId' => $sessionId,
						'OrderStatus' => $status);

		$result = new OpenPayU_Result();
		$result->request = $rq;

		$url = OpenPayU_Configuration::$serviceUrl . "co/openpayu/OrderStatusUpdateRequest";
		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>OpenPayU endpoint for OrderStatusUpdateRequest message : " . $url . "</b><br/>";
		}

		$oauthResult = OpenPayu_OAuth::accessTokenByClientCredentials();
		OpenPayU::setOpenPayuEndPoint($url . "?oauth_token=" . $oauthResult->accessToken);

		$xml = OpenPayU::buildOrderStatusUpdateRequest($rq);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderStatusUpdateRequest message: </b><br/>" .  htmlentities($xml);
		}

		$merchantPosId = OpenPayU_Configuration::$merchantPosId;
		$signatureKey = OpenPayU_Configuration::$signatureKey;
		$response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);
		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderStatusUpdateResponse message: </b><br/>" . htmlentities($response);
		}

		// verify response from PayU service
		$status = OpenPayU::verifyOrderStatusUpdateResponseStatus($response);

		if ($debug) {
			OpenPayU::$outputConsole .= "<br/><b>OrderStatusUpdateResponse status: </b><br/>" . serialize($status);
		}

		$result->status = $status;
		$result->error = $status['StatusCode'];
		$result->success = ($status['StatusCode'] == "OPENPAYU_SUCCESS" ? 1 : 0);
		$result->response = OpenPayU::parseOpenPayUDocument($response);

		return $result;
	}
}

class OpenPayU_ResultOAuth {
	public $url = '';
	public $code = '';
	public $accessToken = '';
	public $payuUserEmail = '';
	public $payuUserId = '';
	public $expiresIn = '';
	public $refreshToken = '';
	public $success = '';
	public $error = '';
}


class OpenPayU_OAuth extends OpenPayUOAuth {
	public static function accessTokenByCode($code, $returnUri, $debug = 1) {

		$url = OpenPayU_Configuration::$serviceUrl . "user/oauth/authorize";

		$result = new OpenPayU_ResultOAuth();
		$result->url = $url;
		$result->code = $code;

		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>retrieve accessToken, authorization code mode, url: " . $url . "</b><br/>";
			OpenPayU::$outputConsole = "<br/><b>return_uri: " . $returnUri . "<br/><br/>";
		}

		try {
			OpenPayU::setOpenPayuEndPoint($url);
			$json = OpenPayuOAuth::getAccessTokenByCode($code, OpenPayU_Configuration::$clientId, OpenPayU_Configuration::$clientSecret, $returnUri);

			$result->accessToken = $json->{"access_token"};
			$result->payuUserEmail = $json->{"payu_user_email"};
			$result->payuUserId = $json->{"payu_user_id"};
			$result->expiresIn = $json->{"expires_in"};
			$result->refreshToken = $json->{"refresh_token"};
			$result->success = 1;
		} catch (Exception $ex) {
			$result->success = 0;
			$result->error = $ex->getMessage();
		}

		return $result;
	}

	public static function accessTokenByClientCredentials($debug = 1) {

		$url = OpenPayU_Configuration::$serviceUrl . "oauth/authorize";

		$result = new OpenPayU_ResultOAuth();
		$result->url = $url;

		OpenPayU::setOpenPayuEndPoint($url);
		if ($debug) {
			OpenPayU::$outputConsole = "<br/><b>retrieve accessToken, client credentials mode, url: " . $url . "</b><br/>";
		}

		try {
			OpenPayU::setOpenPayuEndPoint($url);
			$json = OpenPayUOAuth::getAccessTokenByClientCredentials(OpenPayU_Configuration::$clientId, OpenPayU_Configuration::$clientSecret);

			$result->accessToken = $json->{"access_token"};
			$result->payuUserEmail = $json->{"payu_user_email"};
			$result->payuUserId = $json->{"payu_user_id"};
			$result->expiresIn = $json->{"expires_in"};
			$result->refreshToken = $json->{"refresh_token"};
			$result->success = 1;
		} catch (Exception $ex) {
			$result->success = 0;
			$result->error = $ex->getMessage();
		}

		return $result;
	}
}


?>