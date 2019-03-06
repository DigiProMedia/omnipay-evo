<?php

namespace Omnipay\Evo\Message;

use EvoSnap\CWS\TransactionProcessing\CardSecurityData;

/**
 * Evo Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{

    /**
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        $card = $data['card'];
        // Set SOAP options:
        $options = [
           'features' => 1, // Turn on all features;  see http://php.net/manual/en/soapclient.soapclient.php
           'exceptions' => true, // We want to get PHP Exceptions on errors.
           'cache_wsdl' => $this->getTestMode() ? WSDL_CACHE_NONE : WSDL_CACHE_BOTH
        ];

        try {
            // OK, so now we're going to connect to CWS SIS to log in:
            $sis = new \EvoSnap\CWS\ServiceInformation\SIS($options);// Now we're going to sign on to SIS:
            $session = $sis->SignOnWithToken(
               new \EvoSnap\CWS\ServiceInformation\SignOnWithToken($data['identityToken'])
            );// Now we can transact!
            $tps = new \EvoSnap\CWS\TransactionProcessing\TPS($options);

            // This is a sample transaction object;  there's way more options settable.  Check out the documentation.
            $transactionData = new \EvoSnap\CWS\TransactionProcessing\BankcardTransactionData();
            $transactionData->setAmount($data['total']);
            $transactionData->setCustomerPresent(\EvoSnap\CWS\TransactionProcessing\CustomerPresent::Ecommerce);
            $transactionData->setEntryMode(\EvoSnap\CWS\TransactionProcessing\EntryMode::Keyed);
            $transactionData->setOrderNumber($data['orderId']);
            $transactionData->setSignatureCaptured(false);

            $tenderData = new \EvoSnap\CWS\TransactionProcessing\BankcardTenderData();

            if($this->getCardReference() !== null){
                $tenderData->setPaymentAccountDataToken($this->getCardReference());
            } else {
                $cardData = new \EvoSnap\CWS\TransactionProcessing\CardData($card->getNumber());
                $cardData->setExpire($card->getExpiryDate('my'));
                $cardData->setCardholderName($card->getFirstName() . ' ' . $card->getLastName());
                $tenderData->setCardData($cardData);

                $cardSecurityData = new CardSecurityData();
                $cardSecurityData->setCVData($card->getCvv());
                $cardSecurityData->setCVDataProvided(\EvoSnap\CWS\TransactionProcessing\CVDataProvided::Provided);
                $tenderData->setCardSecurityData($cardSecurityData);
            }


            $transaction = new \EvoSnap\CWS\TransactionProcessing\BankcardTransaction();
            $transaction->setTransactionData($transactionData);
            $transaction->setTenderData($tenderData);

            $authAndCap = new \EvoSnap\CWS\TransactionProcessing\AuthorizeAndCapture(
               $session->getSignOnWithTokenResult(),
               $transaction,
               $data['applicationProfileId'],
               $data['merchantProfileId'],
               $data['workflowId']
            );

            // Now we'll run the Authorize and Capture:
            $authCapResponse = $tps->AuthorizeAndCapture($authAndCap);
            $resp = $authCapResponse->getAuthorizeAndCaptureResult();
        } catch (\SoapFault $error) {
            return new SoapErrorResponse($error, $data);
        }
        return new Response($resp, $data);
    }
}
