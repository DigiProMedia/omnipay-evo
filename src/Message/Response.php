<?php

namespace Omnipay\Evo\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Evo Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var Omnipay\Evo\Message\PurchaseRequest $request The purchase request object.
     */
    protected $response;
    
    public function __construct($request, $response, $data)
    {
        /*
         * 'Transaction ID' => $resp->getTransactionId(),
           'Transaction Status' => $resp->getStatus(),
           'Transaction State' => $resp->getTransactionState(),
         * */

        $this->request = $request;
        $this->response = $response;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return $this->response->getStatusCode() === '1';
    }

    public function getCode()
    {
        return $this->response->getStatusCode() ?? null;
    }

    public function getMessage()
    {
        return $this->response->getStatusMessage();
    }

    public function getTransactionReference() {
        return $this->response->getTransactionId();
    }

    public function getSavedCardReference() {
        return $this->response->getPaymentAccountDataToken();
    }

    public function getRedirectUrl()
    {
        return '';
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {


    }
}
