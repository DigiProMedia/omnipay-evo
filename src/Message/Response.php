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
    
    public function __construct(RequestInterface $request, $data)
    {
        // Decode the response (it's actually very simple).
        $this->data = $this->decode($data);
        $this->request = $request;
    }

    public function isSuccessful()
    {
        // We actually want to always return false, so that the controller then checks
        // for redirection.
        return false;
    }

    public function hasToken()
    {
        return (isset($this->data['status']) && $this->data['status'] === 'ok');
    }

    public function isRedirect()
    {
        // I think we *always* redirect, but only if successful.
        return $this->hasToken();
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return (string) $this->request->getRedirectUrl();
        }

        return '';
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        
        $redirectData = [
            'ClientId' => $this->request->getMerchantId(),
            'StoreType' => $this->request->getMerchantType(),
            'Token' => $this->data['msg'],
            'TranType' => 'Auth',
            'Total' => $this->request->getAmount(),
            'Currency' => $this->request->convertCurrency($this->request->getCurrency()),
            'OrderId' => $this->request->getTransactionId(),
            'ConsumerName' => $this->request->getConsumerName(),
            'ConsumerSurname' => $this->request->getConsumerSurname(),
            'okUrl' => $this->request->getOkUrl(),
            'failUrl' => $this->request->getFailUrl(),
            'pendingURL' => $this->request->getPendingUrl(),
            'lang' => 'en'
        ];
        
        return $redirectData;

    }
    
    protected function decode($data) {
        $tokenParts = [];
        parse_str($data, $tokenParts);
        
        return $tokenParts;
    }

    public function getMessage()
    {
        if ($this->hasToken()) {
            return null;
        }
        if (isset($this->data['msg'])) {
            return $this->data['msg'];
        }
        
        return 'Unable to retrieve error message';
    }
}
