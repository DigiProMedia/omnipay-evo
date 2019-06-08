<?php
declare(strict_types=1);

namespace Omnipay\Evo\Message;

use RecurringPayment\RecurringPayment as RecurringPayment;

/**
 * WePay Purchase Request.
 */
class UpdateRecurringRequest extends RecurringRequest
{

    protected function createPaymentFromData($data)
    {
        $recurringPayments = new RecurringPayment();
        $newPayment = $recurringPayments->getPayment($data['id'], true);
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $newPayment->$key = $value;
                if($key === 'frequency'){
                    $newPayment->frequency_id = null;
                }
            }
        }
        return $newPayment;
    }

    protected function getRecurringPaymentFunction()
    {
        return 'updatePayment';
    }

    protected function verifyRequiredParameters()
    {
        $this->validate('recurringReference');
    }
}
