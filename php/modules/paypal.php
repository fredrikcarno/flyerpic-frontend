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

function setCreditCard() {

	$addr = new PayPal\Api\Address();
	$addr->setLine1('52 N Main ST');
	$addr->setCity('Johnstown');
	$addr->setCountry_code('US');
	$addr->setPostal_code('43210');
	$addr->setState('OH');

	$card = new PayPal\Api\CreditCard();
	$card->setNumber('4417119669820331');
	$card->setType('visa');
	$card->setExpire_month('11');
	$card->setExpire_year('2018');
	$card->setCvv2('874');
	$card->setFirst_name('Joe');
	$card->setLast_name('Shopper');
	$card->setBilling_address($addr);

	return $card;

}

function setPayer($creditCard) {

	$fi = new PayPal\Api\FundingInstrument();
	$fi->setCredit_card($creditCard);

	$payer = new PayPal\Api\Payer();
	$payer->setPayment_method('credit_card');
	$payer->setFunding_instruments(array($fi));

	return $payer;

}

function setAmount() {

	$amountDetails = new PayPal\Api\AmountDetails();
	$amountDetails->setSubtotal('7.41');
	$amountDetails->setTax('0.03');
	$amountDetails->setShipping('0.00');

	$amount = new PayPal\Api\Amount();
	$amount->setCurrency('USD');
	$amount->setTotal('7.47');
	$amount->setDetails($amountDetails);

	return $amount;

}

function setTransaction($amount) {

	$transaction = new PayPal\Api\Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription('This is the payment transaction description.');

	return $transaction;

}

function setPayment($apiContext, $payer, $transaction) {

	$payment = new PayPal\Api\Payment();
	$payment->setIntent('sale');
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	$payment->create($apiContext);

}

function pay() {

	$apiContext	= getContext();
	$card		= setCreditCard();
	$payer		= setPayer($card);
	$amount		= setAmount();
	$transaction	= setTransaction($amount);
	$payment		= setPayment($apiContext, $payer, $transaction);

}

function pay2() {

	$apiContext	= getContext();

	$payment = new PayPal\Api\Payment();
	$payment->create($apiContext);

}