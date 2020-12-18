<?php

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;

class Pay {
	
	var $client;
	
	function __construct($client, $secret) {
		$this->client = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential($client, $secret)
		);
		
		$this->client->setConfig(array('mode' => 'live'));
	}
	
	function create_order($data, $link = '/register/payment/') {
		global $config;
		
		$payer = new Payer();
		$payer->setPaymentMethod("paypal");

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($config['url']['page'] . $data['lang'] . $link . 'confirm/' . $data['transaction'] . '/')->setCancelUrl($config['url']['page'] . $data['lang'] . $link . 'cancel/' . $data['transaction'] . '/');

		$item1 = new Item();
		$item1->setName($data['package']['name'])->setCurrency('EUR')->setQuantity(1)->setPrice($data['package']['price']['value']);
		$itemList = new ItemList();
		$itemList->setItems(array($item1));

		$amount = new Amount();
		$amount->setCurrency("EUR")->setTotal($data['package']['price']['value']);

		$transaction = new Transaction();
		$transaction->setAmount($amount)->setItemList($itemList)->setDescription("WIFIS: " . $data['package']['name'])->setInvoiceNumber($data['transaction']);

		$payment = new Payment();
		$payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

		try {
			$result = array();
			$payment->create($this->client);
			$result['link'] = $payment->getApprovalLink();
			$result['id'] = $payment->getId();
			return $result;
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			return false;
		}	
	}
	
	function check_order($payment_id, $payer_id) {
		$payment = Payment::get($payment_id, $this->client);
		$execution = new PaymentExecution();
		$execution->setPayerId($payer_id);
		try {
			$result = $payment->execute($execution, $this->client);
			$order = $payment->transactions[0];
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			return false;
		}
		if ($result->getState()) {
			return array("state" => $result->getState(), "confirmation" => $order);			
		}
		return false;
	}	
}

$pay = new Pay($config['paypal']['client'], $config['paypal']['secret']);