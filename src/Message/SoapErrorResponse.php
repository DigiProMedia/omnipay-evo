<?php

namespace Omnipay\Evo\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Evo Response
 */
class SoapErrorResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var Omnipay\Evo\Message\PurchaseRequest $request The purchase request object.
     */
    protected $request;
    
    public function __construct($error, $data)
    {
        $this->data = $data;
        $this->error = $error;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function getCode()
    {
        return $this->error->faultcode;
    }

    public function getMessage()
    {
        return $this->error->getMessage();
    }

    public function getTransactionReference() {
        return null;
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
