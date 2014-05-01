<?php

###
# @name			PayPal Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class PayPal {

	private $apiUrl		= 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
	private $paypalUrl	= 'https://sandbox.paypal.com/websrc?cmd=_ap-payment&paykey=';

	private $returnUrl	= '';
	private $cancelUrl	= '';

	function __construct($apiCredentials) {

		$this->apiCredentials = $apiCredentials;

		$this->headers = array(
			'X-PAYPAL-SECURITY-USERID: ' . $this->apiCredentials['username'],
			'X-PAYPAL-SECURITY-PASSWORD: ' . $this->apiCredentials['password'],
			'X-PAYPAL-SECURITY-SIGNATURE: ' . $this->apiCredentials['signature'],
			'X-PAYPAL-APPLICATION-ID: ' . $this->apiCredentials['appID'],
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

		if (!isset($amount)) return false;
		return $amount;

	}

	private function createPayRequest($amount, $user) {

		if (!isset($_SERVER['HTTP_REFERER'], $amount, $user)) exit('Error: Referer, amount or user missing');

		$returnUrl = $_SERVER['HTTP_REFERER'] . '/php/api.php?function=setPayment';
		$cancelUrl = $_SERVER['HTTP_REFERER'];

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

	/*private function setPaymentOptions($payKey) {

		$packet = array(
			'payKey' => $payKey,
			'receiverOptions' => array(
				array(
					'receiver' => array('email' => 'tobias.reich.ich-facilitator@gmail.com')
				),
				array(
					'receiver' => array('email' => 'tobias.reich.ich-test@gmail.com')
				)
			),
			'requestEnvelope' => $this->envelope
		);

		return $this->paypalSend($packet, 'SetPaymentOptions');

	}*/

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

	public function getLink($type, $user, $id) {

		if (!isset($type, $user, $id)) exit('Error: Type, user or id missing');

		# Calculate amount
		$amount = $this->calculateAmount($type, $user);
		if ($amount===false) exit('Error: Can not calculate amount');

		# Create payment request
		$response	= $this->createPayRequest($amount, $user);
		$payKey		= @$response['payKey'];

		# Check payKey
		if (!isset($payKey)) exit('Error: No payKey found');

		# Save info
		$_SESSION['payKey']	= $payKey;
		$_SESSION['payUrl']	= $this->paypalUrl . $payKey;
		$_SESSION['payType']	= $type;
		if ($type==='album') $_SESSION['payAlbumID'] = $id;
		if ($type==='photo') $_SESSION['payPhotoID'] = $id;

		# Return payKey
		return $_SESSION['payUrl'];

	}

}

?>