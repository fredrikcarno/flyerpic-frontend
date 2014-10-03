<?php

###
# @name			PayPal Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class PayPal {

	private $database	= null;

	private $apiUrl		= 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
	private $paypalUrl	= 'https://sandbox.paypal.com/websrc?cmd=_ap-payment&paykey=';

	private $returnUrl	= '';
	private $cancelUrl	= '';

	function __construct($database, $apiCredentials) {

		if (!isset($database, $apiCredentials)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or apiCredentials missing');
			exit('Error: Database or apiCredentials missing');

		}

		# Save database
		$this->database = $database;

		# Verify credentials
		if (!isset($apiCredentials['username'], $apiCredentials['password'], $apiCredentials['signature'], $apiCredentials['appID'])||
			 $apiCredentials['username']===''||$apiCredentials['password']===''||$apiCredentials['signature']===''||$apiCredentials['appID']==='') {

			Log::error($this->database, __METHOD__, __LINE__, 'Missing data in PayPal API credentials');
			exit('Error: Missing data in PayPal API credentials');

		}

		$this->headers = array(
			'X-PAYPAL-SECURITY-USERID: ' . $apiCredentials['username'],
			'X-PAYPAL-SECURITY-PASSWORD: ' . $apiCredentials['password'],
			'X-PAYPAL-SECURITY-SIGNATURE: ' . $apiCredentials['signature'],
			'X-PAYPAL-APPLICATION-ID: ' . $apiCredentials['appID'],
			'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
			'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
		);

		$this->envelope = array(
			'errorLanguage' => 'en_US',
			'detailLevel' => 'ReturnAll'
		);

	}

	private function paypalSend($data, $call) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$call);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

		return json_decode(curl_exec($ch), true);

	}

	private function calculateAmount($type, $user) {

		# Verify parameters
		if (!isset($type, $user, $user['priceperalbum'], $user['percentperprice'])||
			($type!=='album'&&$type!=='photo')||$user['priceperalbum']===''||$user['percentperprice']==='') {

			Log::error($this->database, __METHOD__, __LINE__, 'Missing or corrupt parameters to calculate the amount for the payment');
			return false;

		}

		if ($type==='album') {

			$amount =  array(
				'primary' => $user['priceperalbum'],
				'secondary' => (string)round(($user['priceperalbum']/100)*$user['percentperprice'], 2)
			);

		} else if ($type==='photo') {

			$amount =  array(
				'primary' => $user['priceperphoto'],
				'secondary' => (string)round(($user['priceperphoto']/100)*$user['percentperprice'], 2)
			);

		}

		# Verify amount
		if (!isset($amount, $amount['primary'], $amount['secondary'])||
			 $amount['primary']===''||$amount['secondary']==='') {

			Log::error($this->database, __METHOD__, __LINE__, 'Could not calculate the amount for the payment');
			return false;

		}

		return $amount;

	}

	private function createPayRequest($amount, $user) {

		if (!isset($_SERVER['HTTP_REFERER'], $amount, $user, $user['currencycode'], $user['primarymail'], $user['secondarymail'])||
			 $amount['primary']===''||$amount['secondary']===''||$user['currencycode']===''||$user['primarymail']===''||$user['secondarymail']==='') {

			Log::error($this->database, __METHOD__, __LINE__, 'Referer, amount or user missing');
			exit('Error: Referer, amount or user missing');

		}

		$httpReferer	= str_replace('index.html', '', $_SERVER['HTTP_REFERER']);
		$returnUrl		= $httpReferer . '/php/api.php?function=setPayment';
		$cancelUrl		= $httpReferer;

		$packet = array(
			'actionType' => 'PAY',
			'currencyCode' => $user['currencycode'],
			'receiverList' => array(
				'receiver' => array(
					array(
						'amount' => $amount['primary'],
						'email' => $user['primarymail'],
						'primary' => 'true'
					),
					array(
						'amount' => $amount['secondary'],
						'email' => $user['secondarymail'],
						'primary' => 'false'
					)
				)
			),
			'returnUrl' => $returnUrl,
			'cancelUrl' => $cancelUrl,
			'requestEnvelope' => $this->envelope
		);

		return $this->paypalSend($packet, 'Pay');

	}

	public function checkPayment($payKey) {

		$packet = array(
			'payKey' => $payKey,
			'requestEnvelope' => $this->envelope
		);

		$response	= $this->paypalSend($packet, 'GetPaymentOptions');
		$payed		= (@$response["responseEnvelope"]["ack"] === 'Success' ? true : false);

		if ($payed!==true) return false;
		return true;

	}

	public function getLink($type, $user, $albumID, $photoID = '') {

		if (!isset($type, $user, $albumID)) {
			Log::error($this->database, __METHOD__, __LINE__, 'Type, user or id missing');
			exit('Error: Type, user or id missing');
		}

		# Calculate amount
		$amount = $this->calculateAmount($type, $user);
		if ($amount===false) exit('Error: Can not calculate amount');

		# Create payment request
		$response	= $this->createPayRequest($amount, $user);
		$payKey		= @$response['payKey'];

		# Check payKey
		if (!isset($payKey)) {
			Log::error($this->database, __METHOD__, __LINE__, 'No payKey found');
			exit('Error: No payKey found');
		}

		# Save info
		$_SESSION['payKey']		= $payKey;
		$_SESSION['payUrl']		= $this->paypalUrl . $payKey;
		$_SESSION['payType']	= $type;
		$_SESSION['payAlbumID']	= $albumID;
		if ($type==='photo') $_SESSION['payPhotoID'] = $photoID;

		# Return payKey
		return $_SESSION['payUrl'];

	}

}

?>