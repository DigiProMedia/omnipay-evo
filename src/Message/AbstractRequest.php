<?php
declare(strict_types=1);

namespace Omnipay\Evo\Message;


use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\ResponseInterface;

class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
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


    public function getWorkflowId()
    {
        return $this->getParameter('workflowId');
    }

    public function setWorkflowId($value)
    {
        return $this->setParameter('workflowId', $value);
    }

    public function getData()
    {

        return [
           'applicationProfileId' => $this->getUsername(),
           'merchantProfileId' => $this->getMerchantProfileId(),
           'identityToken' => $this->getPassword(),
           'workflowId' => $this->getWorkflowId(),
           'orderId' => $this->getTransactionId(),
           'total' => $this->getAmount(),
           'card' => $this->getCard()
        ];

    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
    }
}