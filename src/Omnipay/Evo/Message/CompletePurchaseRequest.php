<?php

namespace Omnipay\Evo\Message;

use SimpleXMLElement;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Evo Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        return $this->httpRequest->request->all();
    }

    public function sendData($data)
    {
        return $this->response = new CompleteResponse($this, $data);
    }
}
