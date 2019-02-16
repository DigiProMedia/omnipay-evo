<?php
namespace Omnipay\Evo;

//TODO: Find why I need this line :)
include_once '../vendor/autoload.php';

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    protected $gateway;
    protected $options;
    protected $optionsTransactionReference;
    protected $backupGlobalsBlacklist = ['savedCards', 'recurringReferences'];

    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setPassword('Kodion2018')
           ->setUsername('digipro')
           ->setTestMode(true);
        $this->cardOptions = [
           'card' => $this->getValidCardd()
        ];
        $this->options = [
           'amount' => '10.00',
           'card' => $this->getValidCard()
        ];
    }


    public function testPurchaseSuccess()
    {
        $response = $this->gateway->purchase($this->options)->send();
        $this->verifyPurchaseResult($response);
    }


    public function testPurchaseSavedCardSuccess()
    {
        $options = [
           'isLoggingEnabled'=> true,
           'amount' => 10.00,
           'cardReference' => $this->testCreateCardSuccess(),
           'cvv' => 101
        ];
        $response = $this->gateway->purchase($options)->send();
        $this->verifyPurchaseResult($response);
    }

    public function testCreateCardSuccess()
    {
        $response = $this->gateway->createCard($this->cardOptions)->send();
        $this->verifyCardSuccess($response);
        $this->assertSame('160', $response->getCode());
        $this->assertSame(
           'The customer profile for ' . $response->getCardReference() . '/Example User was successfully created.',
           $response->getMessage()
        );

        $GLOBALS['savedCards'][] = $response->getCardReference();
        return $response->getCardReference();
    }

    public function testCreateCardSuccessSpecialCharacters()
    {
        $options = $this->cardOptions;
        $options['card']['firstName'] = 'Test (Test-Test.)';
        $options['card']['lastName'] = 'Test%#$%&';
        $response = $this->gateway->createCard($options)->send();
        $this->assertSame(
           'The customer profile for ' . $response->getCardReference() . '/Test Test-Test. Test was successfully created.',
           $response->getMessage()
        );
        $this->verifyCardSuccess($response);
        $this->assertSame('160', $response->getCode());

        $GLOBALS['savedCards'][] = $response->getCardReference();
    }

    public function testUpdateCardSuccess()
    {
        $requestOptions = $this->cardOptions;
        $requestOptions['cardReference'] = $this->testCreateCardSuccess();

        $response = $this->gateway->updateCard($requestOptions)->send();
        $this->verifyCardSuccess($response);
        $this->assertSame('161', $response->getCode());
        $this->assertSame(
           'The customer profile for ' . $response->getCardReference() . '/Example User was successfully updated.',
           $response->getMessage()
        );
    }

    public function testDeleteCardSuccess()
    {
        $requestOptions['cardReference'] = $this->testCreateCardSuccess();

        $response = $this->gateway->deleteCard($requestOptions)->send();
        $this->verifyCardSuccess($response);
        $this->assertSame('162', $response->getCode());
        $this->assertSame(
           'The customer profile for ' . $response->getCardReference() . '/Example User was successfully deleted.',
           $response->getMessage()
        );
    }


    public function testPurchaseFailure()
    {
        $this->gateway->setPassword('111');
        $response = $this->gateway->purchase($this->options)->send();
        $this->assertInstanceOf(Message\JSONResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('998', $response->getCode());
        $this->assertSame('Log in failed.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
        $response = $this->gateway->refund($this->options)->send();
        $this->assertInstanceOf(Message\CreditCard\CaptureResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('108', $response->getCode());
        $this->assertSame(
           'Your TEST transaction was successfully refunded. HOWEVER, NO FUNDS WILL BE REFUNDED.',
           $response->getMessage()
        );
    }


    public function testRefundFailure()
    {
        $this->gateway->setPassword('111');
        $response = $this->gateway->refund($this->options)->send();
        $this->assertInstanceOf(Message\CreditCard\CaptureResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('998', $response->getCode());
        $this->assertSame('Log in failed.', $response->getMessage());
    }

    public function testTransactions()
    {
        return $this->getTransactions();
    }

    public function testTransactionsByType($type = null)
    {
        return $this->getTransactions($type);
    }

    public function testTransactionsByTransactionRef()
    {
        $transactionReference = '114310089';
        $options = [
           'startDate' => date('m/d/y', strtotime('last week')),
           'endDate' => date('m/d/y'),
           'transactionReference' => $transactionReference
        ];
        $response = $this->gateway->transactions($options)->send();
        $this->assertInstanceOf(Message\JSONResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('1', $response->getCode());
        $this->assertNotNull($response->getTransactions());
        $this->assertNotCount(0, $response->getTransactions());
        foreach ($response->getTransactions() as $transaction) {
            $this->assertEquals($transactionReference, $transaction['transactionReference']);
        }
        $this->assertSame('Your request has been successfully completed.', $response->getMessage());
    }


    private function verifyCardSuccess($response)
    {
        static::assertFalse(isset($response->getData()['errors']),
           'Errors:' . json_encode($response->getData()['errors'] ?? ''));
        $this->assertInstanceOf(Message\CreditCard\TokenResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getCardReference());
        $this->assertGreaterThan(0, $response->getCardReference());
    }

    private function verifyPurchaseResult($response)
    {
        static::assertFalse(isset($response->getData()['errors']),
           'Errors:' . json_encode($response->getData()['errors'] ?? ''));
        $this->assertInstanceOf(Message\JSONResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('104', $response->getCode());
        $this->assertSame(
           'Your TEST transaction was successfully approved. HOWEVER, A LIVE APPROVAL WAS NOT OBTAINED.',
           $response->getMessage()
        );
    }

}
