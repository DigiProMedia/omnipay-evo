<?php
declare(strict_types = 1);

namespace Omnipay\Evo\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Evo Purchase Response.
 */
class RecurringResponse extends Response implements ResponseInterface {
    public function getMessage() {
        return $this->data['error_description'] ?? $this->getDefaultMessage();
    }

    public function isSuccessful() {
        return $this->getMessage() === $this->getDefaultMessage();
    }

    public function charged() {
        return $this->getData()['charged'] ?? false;
    }

    private function getDefaultMessage() {
        $verb = 'setup';
        if (strpos(get_class($this->request), 'DeleteRecurringRequest') !== false) {
            $verb = 'deleted';
        } elseif (strpos(get_class($this->request), 'UpdateRecurringRequest') !== false) {
            $verb = 'updated';
        }
        return 'Recurring payment ' . $verb . ' successfully.';
    }


    public function getCode()
    {
        return null;
    }

    public function getRecurringReference() {
        return $this->getData()['recurring_id'] ?? null;
    }

    public function getTransactionReference() {
        return $this->getData()['transactionReference'] ?? null;
    }

    public function getSavedCardReference() {
        return $this->request->getData()['card_reference'] ?? null;
    }
}