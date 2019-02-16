<?php

namespace Omnipay\Evo;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Evo';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'password' => '',
            'userName' => '',
            'okUrl' => '',
            'failUrl' => '',
            'pendingUrl' => '',
            'Card' => '',
            'testMode' => false
        );
    }

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

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Evo\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Evo\Message\CompletePurchaseRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Evo\Message\RefundRequest', $parameters);
    }
}
