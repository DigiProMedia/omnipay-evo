<?php

namespace Omnipay\Evo\Message;

use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Evo Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveTokenUrl = 'https://pay.boipa.com/pg/token';
    protected $testTokenUrl = 'https://testvpos.boipa.com/pg/token';
    
    protected $liveRedirectUrl = 'https://pay.boipa.com/fim/paymentgate';
    protected $testRedirectUrl = 'https://testvpos.boipa.com/fim/paymentgate';
    
    protected $liveMerchantType = '3d_pay_hosting';
    protected $testMerchantType = '3d_pay_hosting';
    
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }
    
    public function getMerchantType() {
        return $this->getTestMode() ? $this->testMerchantType : $this->liveMerchantType;
    }
    
    public function setReturnUrl($value)
    {
        $this->setOkUrl($value);
        $this->setFailUrl($value);
        $this->setPendingUrl($value);
    }

    public function setOkUrl($value) {
        return $this->setParameter('okUrl', $value);
    }

    public function getOkUrl() {
        return $this->getParameter('okUrl');
    }

    public function setFailUrl($value) {
        return $this->setParameter('failUrl', $value);
    }

    public function getFailUrl() {
        return $this->getParameter('failUrl');
    }

    public function setPendingUrl($value) {
        return $this->setParameter('pendingUrl', $value);
    }

    public function getPendingUrl() {
        return $this->getParameter('pendingUrl');
    }
    
    public function getConsumerName() {
        $card = $this->getParameter('card');
        return $card->getBillingFirstName();
    }

    public function getConsumerSurname() {
        $card = $this->getParameter('card');
        return $card->getBillingLastName();
    }

    public function getData()
    {

        return [
            'ClientId' => $this->getMerchantId(),
            'Password' => $this->getPassword(),
            'OrderId' => $this->getTransactionId(),
            'Total' => $this->getAmount(),
            'Currency' => $this->convertCurrency($this->getCurrency()),
        ];
        
    }

    public function getTokenUrl()
    {
        return $this->getTestMode() ? $this->testTokenUrl : $this->liveTokenUrl;
    }

    public function getRedirectUrl()
    {
        return $this->getTestMode() ? $this->testRedirectUrl : $this->liveRedirectUrl;
    }

    /**
     * @param SimpleXMLElement $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        // Send the data to the token URL and process the response.
        $tokenResponse = $this->httpClient->post($this->getTokenUrl(), null, $data, ['timeout' => 5])->send();
        $this->response = new Response($this, $tokenResponse->getBody());
        
        return $this->response;
        
    }

    /**
     * Method to convert a currency symbol (eg 'GBP') into the Evo code (eg 826).
     * It handles a valid Evo code being passed in (it returns the same value).
     * If the symbol can't be found, it returns null.
     * @param string $currencySymbol The currency symbol.
     * @return string|null The Evo code for the symbol.
     */
    public function convertCurrency($currencySymbol) {
        $conversion = [
            'PLN' => '985',
            'EUR' => '978',
            'USD' => '840',
            'GBP' => '826',
            'CHF' => '756',
            'DKK' => '208',
            'CAD' => '124',
            'NOK' => '578',
            'SEK' => '752',
            'RUB' => '643',
            'LTL' => '440',
            'RON' => '946',
            'CZK' => '203',
            'JPY' => '392',
            'HUF' => '348',
            'HRK' => '191',
            'UAH' => '980',
            'TRY' => '949'
        ];

        if (in_array($currencySymbol, $conversion)) {
            // It is a valid Evo value.
            return $currencySymbol;
        }
        if (isset($conversion[$currencySymbol])) {
            // It is a symbol that we know the Evo code for.
            return $conversion[$currencySymbol];
        }
        
        return null;
    }
}
