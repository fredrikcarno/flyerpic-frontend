<?php

/**
 * @name		PayPal Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class PayPal {

	var $apiUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
	var $paypalUrl = 'https://sandbox.paypal.com/websrc?cmd=_ap-payment&paykey=';

	function __construct() {

		$this->headers = array(
			'X-PAYPAL-SECURITY-USERID: tobias.reich.ich_api1.gmail.com',
			'X-PAYPAL-SECURITY-PASSWORD: FZA63PGMTMJVHZBY',
			'X-PAYPAL-SECURITY-SIGNATURE: Ag9fhnPqmW9cxAPg6zdjCOvAhbhRA6VAGLePCTZgc7Ymmfw-sifh6ZgE',
			'X-PAYPAL-DEVICE-IPADDRESS: 127.0.0.1',
			'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
			'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON',
			'X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T'
		);

	}

	function getPaymentOptions($paykey) {

	}

	function setPaymentOptions() {

	}

	function _paypalSend($data, $call) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$call);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

		var_dump(curl_exec($ch));

		return json_decode(curl_exec($ch), true);

	}

	function splitPay() {

		// Create the pay request
		$createPacket = array(
			'actionType' => 'PAY',
			'currencyCode' => 'USD',
			'receiverList' => array(
				'receiver' => array(
					array(
						'amount' => '1.00',
						'email' => 'tobias.reich.ich-primary@gmail.com'
					),
					array(
						'amount' => '2.00',
						'email' => 'tobias.reich.ich-second@gmail.com'
					),
				)
			),
			'returnUrl' => 'http://electerious.com/return.html',
			'cancelUrl' => 'http://electerious.com/cancel.html',
			'requestEnvelope' => array(
				'errorLanguage' => 'en_US',
				'detailLevel' => 'ReturnAll'
			)
		);

		$response = $this->_paypalSend($createPacket, 'PAY');
		var_dump($response);

	}

}

?>