<?php

/**
 * @name		PayPal Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class PayPal {

	private $apiUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
	private $paypalUrl = 'https://sandbox.paypal.com/websrc?cmd=_ap-payment&paykey=';

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

		} else {

			exit('Error: Type for payment unknown');

		}

		return $amount;

	}

	private function createPayRequest($amount, $user) {

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
			'returnUrl' => 'http://electerious.com/return.html',
			'cancelUrl' => 'http://electerious.com/cancel.html',
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

	}

	private function getPaymentOptions($payKey) {

		$packet = array(
			'payKey' => $payKey,
			'requestEnvelope' => $this->envelope
		);

		return $this->paypalSend($packet, 'GetPaymentOptions');

	}*/

	public function getLink($type, $user) {

		if (!isset($type, $user)) exit('Error: Type or user missing');

		// Calculate amount
		$amount		= $this->calculateAmount($type, $user);

		// Create payment request
		$response	= $this->createPayRequest($amount, $user);
		$payKey		= @$response['payKey'];

		// Return link
		if (!isset($payKey)) exit('Error: No payKey found');
		else return $this->paypalUrl.$payKey;

	}

}

?>