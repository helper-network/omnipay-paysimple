<?php

namespace Omnipay\PaySimple;

use Omnipay\PaySimple\Message\DeleteBankAccountRequest;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class GatewayTest extends GatewayTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
	}

	public function testCreateCustomer() {
		$request = $this->gateway->createCustomer([
			'FirstName'             => 'Andres',
			'LastName'              => 'Garcia',
			'ShippingSameAsBilling' => "true"
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\CreateCustomerRequest', $request);
		$this->assertSame('Andres', $request->getFirstName());
		$this->assertSame('Garcia', $request->getLastName());
		$this->assertSame('true', $request->getShippingSameAsBilling());
	}

	public function testcreateBank() {
		$request = $this->gateway->createBank([
			'CustomerId'        => '123456',
			'RoutingNumber'     => '131111114',
			'AccountNumber'     => '751111111',
			'BankName'          => 'PaySimple Bank',
			'IsCheckingAccount' => true,
			'IsDefault'         => false,
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\createBankRequest', $request);
		$this->assertSame('123456', $request->getCustomerId());
		$this->assertSame('131111114', $request->getRoutingNumber());
		$this->assertSame('751111111', $request->getAccountNumber());
		$this->assertSame('PaySimple Bank', $request->getBankName());
		$this->assertSame(true, $request->getIsCheckingAccount());
		$this->assertSame(false, $request->getIsDefault());
	}

	public function testPurchase() {
		$request = $this->gateway->purchase([
			'AccountId' => '789123',
			'Amount'    => '50.70'
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\PurchaseRequest', $request);
		$this->assertSame('789123', $request->getAccountId());
		$this->assertSame('50.70', $request->getAmount());
	}

	public function testCreateCard() {
		$card = new CreditCard([
			'number'      => '5454545454545454',
			'expiryMonth' => '13',
			'expiryYear'  => '2021',
		]);

		$request = $this->gateway->createCard([
			'card'       => $card,
			'CustomerId' => '012345',
			'Issuer'     => 13,
			'IsDefault'  => false
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\CreateCardRequest', $request);
		$this->assertSame('012345', $request->getCustomerId());
		$this->assertSame(13, $request->getIssuer());
		$this->assertSame(false, $request->getIsDefault());
	}

	public function testVoid() {
		$request = $this->gateway->void([
			'PaymentId' => 467890
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\VoidRequest', $request);
		$this->assertSame(467890, $request->getPaymentId());
	}

	public function testRefund() {
		$request = $this->gateway->refund([
			'PaymentId' => 467890
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\RefundRequest', $request);
		$this->assertSame(467890, $request->getPaymentId());
	}

	public function testRetrievePayment() {
		$request = $this->gateway->retrievePayment([
			'PaymentId' => 467890
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\RetrievePayment', $request);
		$this->assertSame(467890, $request->getPaymentId());
	}

	public function testRetrieveBankAccounts() {
		$request = $this->gateway->retrieveBankAccounts([
			'CustomerId' => 1234567
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\RetrieveBankAccountsRequest', $request);
		$this->assertSame(1234567, $request->getCustomerId());
	}

	public function testRetrieveCreditCards() {
		$request = $this->gateway->retrieveCreditCards([
			'CustomerId' => 1234567
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\RetrieveCreditCardsRequest', $request);
		$this->assertSame(1234567, $request->getCustomerId());
	}

	public function testDeleteCreditCard() {
		$request = $this->gateway->deleteCreditCard([
			'AccountId' => 635402
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\DeleteCreditCardRequest', $request);
		$this->assertSame(635402, $request->getAccountId());
	}

	public function testDeleteBankAccount() {
		$request = $this->gateway->deleteBankAccount([
			'AccountId' => 635402
		]);

		$this->assertInstanceOf('Omnipay\PaySimple\Message\DeleteBankAccountRequest', $request);
		$this->assertSame(635402, $request->getAccountId());
	}

	public function testBadCredentials()
	{
		$this->setMockHttpResponse('TestInvalidCredentials.txt');

		$request = $this->gateway->purchase([]);
		$stuff = $request->send();
		$this->assertNotTrue($stuff->isSuccessful());

		$this->assertEquals(401, $stuff->getCode());
		$this->assertEquals('Error: Invalid Credentials.', $stuff->getMessage());
	}
}
