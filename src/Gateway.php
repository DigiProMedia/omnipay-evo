<?php

namespace Omnipay\Evo;

use Omnipay\Common\AbstractGateway;
use Omnipay\Evo\Message\PurchaseRequest;
use Omnipay\Evo\Message\RefundRequest;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Evo';
    }

    public function getDefaultParameters()
    {
        return [
           'merchantProfileId' => '',
           'password' => '',
           'userName' => '',
           'workflowId' => '',
           'okUrl' => '',
           'failUrl' => '',
           'pendingUrl' => '',
           'card' => '',
           'testMode' => false
        ];
    }

    public function getMerchantProfileId()
    {
        return $this->getParameter('merchantProfileId');
    }

    public function setMerchantProfileId($value)
    {
        return $this->setParameter('merchantProfileId', $value);
    }

    public function getUsername()
    {
        return $this->getParameter('userName');
    }

    public function setUsername($value)
    {
        return $this->setParameter('userName', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getWorkflowId()
    {
        return $this->getParameter('workflowId');
    }

    public function setWorkflowId($value)
    {
        return $this->setParameter('workflowId', $value);
    }

    public function getOkUrl()
    {
        return $this->getParameter('okUrl');
    }

    public function setOkUrl($value)
    {
        return $this->setParameter('okUrl', $value);
    }

    public function getFailUrl()
    {
        return $this->getParameter('failUrl');
    }

    public function setFailUrl($value)
    {
        return $this->setParameter('failUrl', $value);
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function refund(array $parameters = [])
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }



    /*public function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }*/

    function authorize(array $options = [])
    {
        // TODO: Implement authorize() method.
    }

}
