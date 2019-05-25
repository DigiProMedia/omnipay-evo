<?php
declare(strict_types=1);

namespace Omnipay\Evo\Message;

use RecurringPayment\Payment as Payment;
use RecurringPayment\RecurringPayment as RecurringPayment;
use RecurringPayment\ScheduledTask as RecurringPaymentRunner;

class RecurringRequest extends AbstractRequest
{
    public function getData()
    {
        $this->verifyRequiredParameters();

        $data = [
           'frequency' => $this->lookUpFrequency($this->getFrequency()),
           'start_date' => $this->getStartDate(),
           'next_date' => $this->getNextDate(),
           'description' => $this->getDescription() ?? 'Recurring Payment',
           'email' => $this->getEmail(),
           'total_count' => $this->getTotalCount(),
           'amount' => $this->getAmount(),
           'card_reference' => $this->getCardReference(),
           'gateway' => 'Evo_CreditCard',
           'gateway_url' => $this->getURL(),
           'gateway_username' => $this->getUsername(),
           'gateway_password' => $this->getPassword(),
           'test_mode' => $this->getTestMode() === true ? 1 : 0,
           'location_id' => $this->getLocationId(),
           'channel_id' => $this->getChannelId(),
           'sub_domain' => $this->getSubDomain(),
           'invoice' => $this->getInvoice(),
           'additional_data' => json_encode(['merchantProfileId' => $this->getMerchantProfileId()])
        ];

        if ($this->getRecurringReference() !== null) {
            $data['id'] = $this->getRecurringReference();
        }

        return $data;
    }

    public function sendData($data)
    {
        try {
            $newPayment = $this->createPaymentFromData($data);
            $recurringPayments = new RecurringPayment();
            $function = $this->getRecurringPaymentFunction();
            //TODO: Does this already have a payment?
            $newPayment = $recurringPayments->$function($newPayment);
            $now = date('Y-m-d');
            $responseData = [
               'successful' => true,
               'recurring_id' => $newPayment->id,
               'charged' => false
            ];
            if ($newPayment->next_date <= $now) {
                $recurringPaymentRunner = new RecurringPaymentRunner();
                $response = $recurringPaymentRunner->processPayment($newPayment);
                $responseData = array_merge_recursive($responseData, (array)$response['result']);
                $responseData['checkout_id'] = $responseData['transactionReference'];
                $responseData['successful'] = $response['success'];
                $responseData['charged'] = $response['success'];
            }
            $responseData['recurring_id'] = $newPayment->id;
        } catch (\Exception $e) {
            $responseData = [
               'successful' => false,
               'recurring_id' => null,
               'charged' => false,
               'error_description' => $e->getMessage()
            ];
        }
        return new RecurringResponse($this, null, $responseData);
    }

    public function getFrequency()
    {
        return $this->getParameter('frequency');
    }

    public function setFrequency($value)
    {
        ;
        return $this->setParameter('frequency', $value);
    }

    public function getStartDate()
    {
        return $this->getParameter('startDate');
    }

    public function setStartDate($value)
    {
        return $this->setParameter('startDate', $value);
    }

    public function getNextDate()
    {
        return $this->getParameter('nextDate');
    }

    public function setNextDate($value)
    {
        return $this->setParameter('nextDate', $value);
    }

    public function getTotalCount()
    {
        return $this->getParameter('totalCount');
    }

    public function setTotalCount($value)
    {
        return $this->setParameter('totalCount', $value);
    }

    public function getLocationId()
    {
        return $this->getParameter('locationID');
    }

    public function setLocationId($value)
    {
        return $this->setParameter('locationID', $value);
    }

    public function getChannelId()
    {
        return $this->getParameter('channelID');
    }

    public function setChannelId($value)
    {
        return $this->setParameter('channelID', $value);
    }

    public function getSubDomain()
    {
        return $this->getParameter('subDomain');
    }

    public function setSubDomain($value)
    {
        return $this->setParameter('subDomain', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getInvoice()
    {
        return $this->getParameter('invoice');
    }

    public function setInvoice($value)
    {
        return $this->setParameter('invoice', $value);
    }

    private function lookUpFrequency($value)
    {
        if ($value === null) {
            return null;
        }
        $value = strtolower($value);
        $frequencyLookUp = [
           strtolower('One-Time') => 'once',
           strtolower('once') => 'once',
           strtolower('useOnce') => 'once',
           strtolower('Weekly') => 'weekly',
           strtolower('Daily') => 'daily',
           strtolower('Bi-Weekly') => 'biweekly',
           strtolower('biweekly') => 'biweekly',
           strtolower('Monthly') => 'monthly',
           strtolower('Annually') => 'yearly',
           strtolower('Yearly') => 'yearly'
        ];

        return $frequencyLookUp[$value];
    }

    protected function createPaymentFromData($data)
    {
        $newPayment = new  Payment();
        foreach ($data as $key => $value) {
            $newPayment->$key = $value;
        }
        return $newPayment;
    }

    protected function getRecurringPaymentFunction()
    {
        return 'addPayment';
    }

    protected function verifyRequiredParameters()
    {
        $this->validate('frequency', 'startDate', 'totalCount', 'email');
    }

    private function getURL()
    {
        if (!isset($_SERVER['SERVER_NAME'])) {
            return 'http://localhost/';
        }
        $protocal = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        $serverName = $_SERVER['SERVER_NAME'];
        $port = '';
        if (($protocal == 'https://' && $_SERVER['SERVER_PORT'] != 443) || ($protocal == 'http://' && $_SERVER['SERVER_PORT'] != 80)) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        return $protocal . $serverName . $port . '/';
    }
}
