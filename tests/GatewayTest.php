<?php

namespace Omnipay\PaySimple;

use Omnipay\PaySimple\Message\DeleteBankAccountRequest;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;
use Omnipay\PaySimple\Message\createBankRequest;
use Omnipay\PaySimple\Message\DeleteCreditCardRequest;
use Omnipay\PaySimple\Message\RetrieveCreditCardsRequest;
use Omnipay\PaySimple\Message\RetrieveBankAccountsRequest;
use Omnipay\PaySimple\Message\RetrievePayment;
use Omnipay\PaySimple\Message\RefundRequest;
use Omnipay\PaySimple\Message\VoidRequest;
use Omnipay\PaySimple\Message\CreateCardRequest;
use Omnipay\PaySimple\Message\PurchaseRequest;
use Omnipay\PaySimple\Message\CreateCustomerRequest;

class GatewayTest extends GatewayTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
	}

	public function testCreateCustomer(): void {
		$request = $this->gateway->createCustomer([
			'FirstName'             => 'Andres',
			'LastName'              => 'Garcia',
			'ShippingSameAsBilling' => 'true'
		]);

		$this->assertInstanceOf(CreateCustomerRequest::class, $request);
		$this->assertSame('Andres', $request->getFirstName());
		$this->assertSame('Garcia', $request->getLastName());
		$this->assertSame('true', $request->getShippingSameAsBilling());
	}

	public function testcreateBank(): void {
		$request = $this->gateway->createBank([
			'CustomerId'        => '123456',
			'RoutingNumber'     => '131111114',
			'AccountNumber'     => '751111111',
			'BankName'          => 'PaySimple Bank',
			'AccountType' => 'checking',
			'IsDefault'         => false,
		]);

		$this->assertInstanceOf(createBankRequest::class, $request);
		$this->assertSame('123456', $request->getCustomerId());
		$this->assertSame('131111114', $request->getRoutingNumber());
		$this->assertSame('751111111', $request->getAccountNumber());
		$this->assertSame('PaySimple Bank', $request->getBankName());
		$this->assertSame('checking', $request->getAccountType());
		$this->assertFalse($request->getIsDefault());
	}

	public function testPurchase(): void {
		$request = $this->gateway->purchase([
			'AccountId' => '789123',
			'Amount'    => '50.70'
		]);

		$this->assertInstanceOf(PurchaseRequest::class, $request);
		$this->assertSame('789123', $request->getAccountId());
		$this->assertSame('50.70', $request->getAmount());
	}

	public function testCreateCard():void {
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

		$this->assertInstanceOf(CreateCardRequest::class, $request);
		$this->assertSame('012345', $request->getCustomerId());
		$this->assertSame(13, $request->getIssuer());
		$this->assertSame(false, $request->getIsDefault());
	}

	public function testVoid(): void {
		$request = $this->gateway->void([
			'TransactionId' => 467890
		]);

		$this->assertInstanceOf(VoidRequest::class, $request);
		$this->assertSame(467890, $request->getTransactionId());
	}

	public function testRefund(): void {
		$request = $this->gateway->refund([
			'TransactionId' => 467890
		]);

		$this->assertInstanceOf(RefundRequest::class, $request);
		$this->assertSame(467890, $request->getTransactionId());
	}

	public function testRetrievePayment(): void {
		$request = $this->gateway->retrievePayment([
			'TransactionId' => 467890
		]);

		$this->assertInstanceOf(RetrievePayment::class, $request);
		$this->assertSame(467890, $request->getTransactionId());
	}

	public function testRetrieveBankAccounts(): void {
		$request = $this->gateway->retrieveBankAccounts([
			'CustomerId' => 1234567
		]);

		$this->assertInstanceOf(RetrieveBankAccountsRequest::class, $request);
		$this->assertSame(1234567, $request->getCustomerId());
	}

	public function testRetrieveCreditCards(): void {
		$request = $this->gateway->retrieveCreditCards([
			'CustomerId' => 1234567
		]);

		$this->assertInstanceOf(RetrieveCreditCardsRequest::class, $request);
		$this->assertSame(1234567, $request->getCustomerId());
	}

	public function testDeleteCreditCard(): void {
		$request = $this->gateway->deleteCreditCard([
			'AccountId' => 635402
		]);

		$this->assertInstanceOf(DeleteCreditCardRequest::class, $request);
		$this->assertSame(635402, $request->getAccountId());
	}

	public function testDeleteBankAccount(): void {
		$request = $this->gateway->deleteBankAccount([
			'AccountId' => 635402
		]);

		$this->assertInstanceOf(DeleteBankAccountRequest::class, $request);
		$this->assertSame(635402, $request->getAccountId());
	}

	public function testBadCredentials(): void {
		$this->setMockHttpResponse('TestInvalidCredentials.txt');

		$request = $this->gateway->purchase([]);
		$stuff = $request->send();
		$this->assertNotTrue($stuff->isSuccessful());

		$this->assertEquals(401, $stuff->getCode());
		$this->assertEquals('Error: Invalid Credentials.', $stuff->getMessage());
	}
}
