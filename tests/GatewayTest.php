<?php

namespace Omnipay\Evo;

include_once '../vendor/autoload.php';

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    protected $gateway;
    protected $options;
    protected $cardOptions;
    protected $optionsWithAmount;
    protected $backupGlobalsBlacklist = ['savedCards', 'recurringReferences'];

    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setPassword('PHNhbWw6QXNzZXJ0aW9uIE1ham9yVmVyc2lvbj0iMSIgTWlub3JWZXJzaW9uPSIxIiBBc3NlcnRpb25JRD0iXzE1NDI5OTRhLTI2OWItNGU4Mi04MjljLTdkODRjMjMxZmE0OSIgSXNzdWVyPSJJcGNBdXRoZW50aWNhdGlvbiIgSXNzdWVJbnN0YW50PSIyMDE4LTA1LTAxVDIwOjQyOjA1LjM3MVoiIHhtbG5zOnNhbWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjEuMDphc3NlcnRpb24iPjxzYW1sOkNvbmRpdGlvbnMgTm90QmVmb3JlPSIyMDE4LTA1LTAxVDIwOjQyOjA1LjM3MFoiIE5vdE9uT3JBZnRlcj0iMjAyMS0wNS0wMVQyMDo0MjowNS4zNzBaIj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBZHZpY2U+PC9zYW1sOkFkdmljZT48c2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PHNhbWw6U3ViamVjdD48c2FtbDpOYW1lSWRlbnRpZmllcj4xRkY4NjgxRkE3NDAwMDAxPC9zYW1sOk5hbWVJZGVudGlmaWVyPjwvc2FtbDpTdWJqZWN0PjxzYW1sOkF0dHJpYnV0ZSBBdHRyaWJ1dGVOYW1lPSJTQUsiIEF0dHJpYnV0ZU5hbWVzcGFjZT0iaHR0cDovL3NjaGVtYXMuaXBjb21tZXJjZS5jb20vSWRlbnRpdHkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlPjFGRjg2ODFGQTc0MDAwMDE8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0iU2VyaWFsIiBBdHRyaWJ1dGVOYW1lc3BhY2U9Imh0dHA6Ly9zY2hlbWFzLmlwY29tbWVyY2UuY29tL0lkZW50aXR5Ij48c2FtbDpBdHRyaWJ1dGVWYWx1ZT44MzFiMGVmYi0yZjg0LTRjNjctODM3Ni01MDc1YTlhZDVhMzY8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0ibmFtZSIgQXR0cmlidXRlTmFtZXNwYWNlPSJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcyI+PHNhbWw6QXR0cmlidXRlVmFsdWU+MUZGODY4MUZBNzQwMDAwMTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjwvc2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PFNpZ25hdHVyZSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+PFNpZ25lZEluZm8+PENhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiPjwvQ2Fub25pY2FsaXphdGlvbk1ldGhvZD48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIj48L1NpZ25hdHVyZU1ldGhvZD48UmVmZXJlbmNlIFVSST0iI18xNTQyOTk0YS0yNjliLTRlODItODI5Yy03ZDg0YzIzMWZhNDkiPjxUcmFuc2Zvcm1zPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSI+PC9UcmFuc2Zvcm0+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyI+PC9UcmFuc2Zvcm0+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSI+PC9EaWdlc3RNZXRob2Q+PERpZ2VzdFZhbHVlPmZYQnFFMU1HV0ZxM0cvbEkvMk1Sa000cUV3QT08L0RpZ2VzdFZhbHVlPjwvUmVmZXJlbmNlPjwvU2lnbmVkSW5mbz48U2lnbmF0dXJlVmFsdWU+b3lzS0h5ZzJHc3JzOHhiY1NycmRock1kQm5WOFRJditEUjFzOUEwQVpiZzZObHZqdGxIZEY4eWp5YnlhWkdody9mMEx1ckdkYjZ5OEplWjUzcE9sQXJmUFk3ZVdlNXNheXdSUHI3SzNlUkdxSDlFczZMVzE4U3h2Q3RYVmY5SUhpQjBXSytLMGt0THFFcGFEUG1saDdyYUhWQzJNWVZOK0QxdDQrVXd0TlFudmFCbDFxcVN6VFNzS3k1UTNwU05yMHcybTcrV0hZLzE3UXNINUc5RTdoWVRxRWZPVUR4N0g4eTdPTEt5bS9oOVREYWorSjllRDErVS9XZkpCdFhGVUF6YURnenB6TlNlUDBaTS9QZG9laUtGMHhpekx3RWNSdC9DU0VXY3FqbkhxU1lBNVV3THYyTlBLeHkvbitpM1pCU05mVGJoTmxQdS9mK3cxdmVqTGNBPT08L1NpZ25hdHVyZVZhbHVlPjxLZXlJbmZvPjxvOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2UgeG1sbnM6bz0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzLzIwMDQvMDEvb2FzaXMtMjAwNDAxLXdzcy13c3NlY3VyaXR5LXNlY2V4dC0xLjAueHNkIj48bzpLZXlJZGVudGlmaWVyIFZhbHVlVHlwZT0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzL29hc2lzLXdzcy1zb2FwLW1lc3NhZ2Utc2VjdXJpdHktMS4xI1RodW1icHJpbnRTSEExIj5iQkcwU0cvd2RCNWJ4eVpyYjEvbTVLakhLMU09PC9vOktleUlkZW50aWZpZXI+PC9vOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2U+PC9LZXlJbmZvPjwvU2lnbmF0dXJlPjwvc2FtbDpBc3NlcnRpb24+')
           ->setUsername('699484')
           ->setMerchantProfileId('DigiProSampleMerchant')
           ->setTestMode(true);
        $this->cardOptions = [
           'card' => $this->getValidCard()
        ];
        $this->optionsWithAmount = [
           'amount' => '10.00',
           'card' => $this->getValidCard()
        ];
    }


    public function testPurchaseSuccess()
    {
        $response = $this->gateway->purchase($this->optionsWithAmount)->send();
        $this->verifyPurchaseResult($response);
    }


    public function testPurchaseSavedCardSuccess()
    {
        $options = [
           'isLoggingEnabled' => true,
           'amount' => 10.00,
           'cardReference' => $this->testCreateCardSuccess(),
           'cvv' => 101
        ];
        $response = $this->gateway->purchase($options)->send();
        $this->verifyPurchaseResult($response);
    }

    public function testPurchaseFailure()
    {
        $this->gateway->setPassword('111');
        $response = $this->gateway->purchase($this->optionsWithAmount)->send();
        $this->assertInstanceOf(Message\SoapErrorResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('a:InvalidSecurityToken', $response->getCode());
        $this->assertSame('An invalid security token was provided.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
        $response = $this->gateway->refund($this->optionsWithAmount)->send();
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
        $response = $this->gateway->refund($this->optionsWithAmount)->send();
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
        $this->assertInstanceOf(Message\Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('1', $response->getCode());
        $this->assertSame('APPROVED', $response->getMessage()
        );
    }

    public function getValidCard()
    {
        $cardData = parent::getValidCard();
        $cardData['cvv'] = '111'; //Valid
        return $cardData;
    }

}