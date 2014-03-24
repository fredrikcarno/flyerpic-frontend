<?php

/**
 * @name		PayPal Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

function getContext($clientId, $clientSecret) {

	$oauthCredential = new PayPal\Auth\OAuthTokenCredential($clientId, $clientSecret);
	$apiContext		= new PayPal\Rest\ApiContext($oauthCredential);

	return $apiContext;

}

function setRedirect($return, $cancel) {

	$redirect = new PayPal\Api\RedirectUrls();
	$redirect->setReturn_url($return);
	$redirect->setCancel_url($cancel);

	return $redirect;

}

function setPayer() {

	$payer = new PayPal\Api\Payer();
	$payer->setPayment_method('paypal');

	return $payer;

}

function setTransaction($price, $currency = 'USD', $description = 'No description') {

	$amount = new PayPal\Api\Amount();
	$amount->setTotal($price);
	$amount->setCurrency($currency);

	$transaction = new PayPal\Api\Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription($description);

	return $transaction;

}

function setPayment($redirect, $payer, $transaction) {

	$payment = new PayPal\Api\Payment();
	$payment->setIntent('sale');
	$payment->setRedirect_urls($redirect);
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	return $payment;

}

function getPaymentLink($payment) {

	$redirectUrl = null;

	foreach ($payment->getLinks() as $link) {
		if ($link->getRel() == 'approval_url') $redirectUrl = $link->getHref();
	}

	return $redirectUrl;

}

function getPayPalLink() {

	global $ini;

	$apiContext	= getContext($ini['acct1.ClientId'], $ini['acct1.ClientSecret']);
	$redirect	= setRedirect('http://electerious.com/#return', 'http://electerious.com/#cancel');
	$payer		= setPayer();
	$transaction	= setTransaction('9', 'USD');
	$payment		= setPayment($redirect, $payer, $transaction);

	try {

		$payment->create($apiContext);

	} catch (PayPal\Exception\PPConnectionException $ex) {

		echo 'hallo';
		echo "Exception: " . $ex->getMessage() . PHP_EOL;
		var_dump($ex->getData());
		exit(1);

	}

	$redirectUrl = getPaymentLink($payment);

	return $redirectUrl;

}