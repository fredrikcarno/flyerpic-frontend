<?php

/**
 * @name		PayPal Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

function getContext() {

	global $ini;

	$oauthCredential = new PayPal\Auth\OAuthTokenCredential($ini['acct1.ClientId'], $ini['acct1.ClientSecret']);
	$apiContext		= new PayPal\Rest\ApiContext($oauthCredential);

	return $apiContext;

}

function setRedirect() {

	$redirect = new PayPal\Api\RedirectUrls();
	$redirect->setReturn_url('https://electerious.com#return');
	$redirect->setCancel_url('https://electerious.com#cancel');

	return $redirect;

}

function setPayer() {

	$payer = new PayPal\Api\Payer();
	$payer->setPayment_method('paypal');

	return $payer;

}

function setTransaction() {

	$amount = new PayPal\Api\Amount();
	$amount->setCurrency('USD');
	$amount->setTotal('7');

	$transaction = new PayPal\Api\Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription('This is the payment transaction description.');

	return $transaction;

}

function setPayment($apiContext, $redirect, $payer, $transaction) {

	$payment = new PayPal\Api\Payment();
	$payment->setIntent('sale');
	$payment->setRedirect_urls($redirect);
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	return $payment;

}

function pay() {

	$apiContext	= getContext();
	$redirect	= setRedirect();
	$payer		= setPayer();
	$transaction	= setTransaction();
	$payment		= setPayment($apiContext, $redirect, $payer, $transaction);

	try {

		$payment->create($apiContext);

	} catch (PayPal\Exception\PPConnectionException $ex) {

		echo 'hallo';
		echo "Exception: " . $ex->getMessage() . PHP_EOL;
		var_dump($ex->getData());
		exit(1);

	}

}