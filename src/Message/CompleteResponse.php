<?php

namespace Omnipay\Evo\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Evo Response
 */
class CompleteResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var Omnipay\Evo\Message\CompletePurchaseRequest $request The purchase request object.
     */
    protected $request;
    
    /**
     * @var bool $isHashValid Says whether the hash validation worked or not.
     */
    protected $isHashValid;

    public function __construct(RequestInterface $request, $data)
    {
        // Decode the response (it's actually very simple).
        $this->data = is_array($data) ? $data : [];
        $this->request = $request;
        $this->validateHash();
    }

    public function isSuccessful()
    {
        // Check that the hash validation worked, and that the response was "Approved".
        return ($this->isHashValid && isset($this->data['Response']) && $this->data['Response'] === 'Approved');
    }
    
    public function getMessage() {
        if ($this->isSuccessful()) {
            return null;
        }
        if ($this->isHashValid) {
            return (isset($this->data['ErrMsg']) ? $this->data['ErrMsg'] : null);
        }
        
        return 'Hash validation failed';
    }
    
    public function getTransactionReference() {
        return ($this->isSuccessful() && isset($this->data['TransId']) ? $this->data['TransId'] : null);
    }

    public function getTransactionId() {
        return $this->data['OrderId'];
    }

    public function getRedirectUrl() {
        return null;
    }
    
    public function getRedirectMethod() {
        return 'GET';
    }
    
    public function getRedirectData() {
        return [];
    }

    /**
     * This method is used to validate that the posted message is valid. It builds a hash
     * value of certain values in the message. Then, the built hash is checked against
     * the hash in the message - if different, we will raise an error. 
     */
    protected function validateHash() {
        $this->isHashValid = false;
        $localHash = '';
        // Check that the list of values to use in the hash, and their generated hash
        // value are present.
        if (isset($this->data['HASHPARAMS']) && isset($this->data['HASH'])) {
            // Loop through each parameter we've been given and append it to our hash
            // (with no separators).
            $params = explode(':', $this->data['HASHPARAMS']);
            foreach ($params as $param) {
                if (isset($this->data[$param])) {
                    $localHash .= $this->data[$param];
                }
            }
            // Then append our storeKey (which isn't included in the post), again with
            // no separator.
            $localHash .= $this->request->getPassword();
            // Encode our hash value.
            $localHash = base64_encode(sha1($localHash, true));
            // Compare our hash with their hash.
            $this->isHashValid = $localHash === $this->data['HASH'];
        }
    }
    
    public function getCode() {
        return $this->data['Response'];
    }

}