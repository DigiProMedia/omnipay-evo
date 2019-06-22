<?php
declare(strict_types = 1);
namespace Omnipay\Evo\Message;

use Guzzle\Http\Exception\BadResponseException;
use Omnipay\Evo\Message\AbstractRequest;
use Omnipay\Evo\Message\RecurringResponse;
use RecurringPayment\RecurringPayment as RecurringPayment;
use RecurringPayment\Payment as Payment;

class DeleteRecurringRequest extends AbstractRequest {
    public function getData() {
        $this->validate('recurringReference');
        $data = ['recurring_reference' => $this->getRecurringReference()];
        return $data;
    }

    public function sendData($data) {
        $recurringPayments = new RecurringPayment();
        $success = $recurringPayments->deletePayment($data['recurring_reference']);
        $responseData['recurring_id'] = $data['recurring_reference'];
        $responseData['successful'] = $success;
        if(!$success) {
            $responseData['RequestResult'] = [
               'ResultMessage' => 'Recurring payment not found.',
               'ResultCode' => '00'
            ];
        } else {
            $responseData['RequestResult'] = [
               'ResultMessage' => 'Recurring payment deleted successfully.',
               'ResultCode' => '00'
            ];
        }
        return new RecurringResponse($this, null, $responseData);
    }
}
