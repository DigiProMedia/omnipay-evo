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
    protected $request;
    
    public function __construct($request, $data)
    {
        /*
         * 'Transaction ID' => $resp->getTransactionId(),
           'Transaction Status' => $resp->getStatus(),
           'Transaction State' => $resp->getTransactionState(),
         * */

        $this->data = $data;
        $this->request = $request;
    }

    public function isSuccessful()
    {
        return $this->request->getStatusCode() === '1';
    }

    public function getCode()
    {
        return $this->request->getStatusCode();
    }

    public function getMessage()
    {
        return $this->request->getStatusMessage();
    }

    public function getTransactionReference() {
        return $this->request->getTransactionId();
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
