<?php

/**
 * Fee Payment Helper Service
 *
 * This exists as a fairly thin wrapper around CPMS’ own SDK
 * because it's otherwise still a *bit* verbose to just dump
 * in controllers. Also, logically we’re probably always going
 * to be paying fee objects, so tieing CPMS + fees together makes
 * sense from an OLCS perspective
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Cpms;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Common\Service\Entity\FeePaymentEntityService;
use Common\Service\Entity\PaymentEntityService;
use Common\Service\Entity\FeeEntityService;
use Common\Util\LoggerTrait;
use CpmsClient\Service\ApiService;
use Common\Service\Listener\FeeListenerService;

/**
 * Fee Payment Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeePaymentCpmsService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        LoggerTrait;

    const PAYMENT_SUCCESS      = 801;
    const PAYMENT_FAILURE      = 802;
    const PAYMENT_CANCELLATION = 807;

    const RESPONSE_SUCCESS = '000';

    protected function getClient()
    {
        return $this->getServiceLocator()->get('cpms\service\api');
    }

    public function initiateCardRequest($customerReference, $salesReference, $redirectUrl, array $fees)
    {
        $amount = array_reduce(
            $fees,
            function ($carry, $item) {
                $carry += $item['amount'];
                return $carry;
            }
        );

        // @TODO product ref shouldn't have to come from a whitelist...
        $productReference = 'GVR_APPLICATION_FEE';

        $params = [
            // @NOTE CPMS rejects ints as 'missing', so we have to force a string...
            'customer_reference' => (string)$customerReference,
            'scope' => ApiService::SCOPE_CARD,
            'disable_redirection' => true,
            'redirect_uri' => $redirectUrl,
            'payment_data' => [
                [
                    'amount' => $amount,
                    'sales_reference' => $salesReference,
                    'product_reference' => $productReference
                ]
            ]
        ];

        $response = $this->getClient()->post('/api/payment/card', ApiService::SCOPE_CARD, $params);

        $payment = $this->getServiceLocator()
            ->get('Entity\Payment')
            ->save(
                [
                    // yes, 'redirection_data' really is correct...
                    'guid' => $response['redirection_data'],
                    'status' => PaymentEntityService::STATUS_OUTSTANDING
                ]
            );

        foreach ($fees as $fee) {
            $this->getServiceLocator()
                ->get('Entity\FeePayment')
                ->save(
                    [
                        'payment' => $payment['id'],
                        'fee' => $fee['id'],
                        'feeValue' => $fee['amount']
                    ]
                );
        }

        return $response;
    }

    /**
     * Record a cash payment in CPMS
     *
     * @param string $customerReference
     * @param string $salesReference
     * @param float $amount
     * @param array $receiptDate (from DateSelect)
     * @param string $payer
     * @param string $slipNo
     * @return boolean success
     */
    public function recordCashPayment(
        $fee,
        $customerReference,
        $salesReference,
        $amount,
        $receiptDate,
        $payer,
        $slipNo
    ) {
        // Partial payments are not supported. The form validation will normally catch
        // this but it relies on a hidden field so we have a secondary check here
        if ($fee['amount'] != $amount) {
            throw new PaymentInvalidAmountException("Amount must match the fee due");
        }

        $productReference = 'GVR_APPLICATION_FEE';
        $receiptDate = $this->formatReceiptDate($receiptDate);
        $params = [
            'customer_reference' => (string)$customerReference,
            'scope' => ApiService::SCOPE_CASH,
            'total_amount' => $amount,
            'payment_data' => [
                [
                    'amount' => $amount,
                    'sales_reference' => $salesReference,
                    'product_reference' => $productReference,
                    'payer_details' => $payer, // not sure this is supported for CASH payments
                    'payment_reference' => [
                        'slip_number' => (string)$slipNo,
                        'receipt_date' => $receiptDate,
                    ],
                ]
            ]
        ];

        $response = $this->getClient()->post('/api/payment/cash', ApiService::SCOPE_CASH, $params);

        if ($this->isSuccessfulResponse($response)) {
            $data = [
                'feeStatus'      => FeeEntityService::STATUS_PAID,
                'receivedDate'   => $receiptDate,
                'receiptNo'      => $response['receipt_reference'],
                'paymentMethod'  => FeePaymentEntityService::METHOD_CASH,
                'receivedAmount' => $amount,
                'payer'          => $payer,
                'slipNo'         => $slipNo,
            ];

            $this->getServiceLocator()
                ->get('Entity\Fee')
                ->forceUpdate($fee['id'], $data);

            $this->getServiceLocator()->get('Listener\Fee')->trigger(
                $fee['id'],
                FeeListenerService::EVENT_PAY
            );
            return true;
        }
        return false;
    }

    /**
     * Small helper to check if response was successful
     *
     * @param array $response response data
     * @return boolean
     */
    protected function isSuccessfulResponse(array $response)
    {
        return (
            isset($response['code'])
            && $response['code'] === self::RESPONSE_SUCCESS
            && isset($response['receipt_reference'])
        );
    }

    /**
     * Format a date as required by CPMS payment reference fields
     *
     * @param array|DateTime $date
     * @return string
     */
    public function formatReceiptDate($date)
    {
        if (is_array($date)) {
            $date = $this->getServiceLocator()->get('Helper\Date')->getDateObjectFromArray($date);
        }
        return $date->format('d-m-Y');
    }

    public function handleResponse($data, $fees)
    {
        $reference      = $data['receipt_reference'];
        $paymentService = $this->getServiceLocator()->get('Entity\Payment');
        $client         = $this->getServiceLocator()->get('cpms\service\api');

        /**
         * 1) Check what status we think this payment is currently in
         */
        $payment = $paymentService->getDetails($reference);

        if ($payment === false) {
            throw new PaymentNotFoundException('Payment not found');
        }

        if ($payment['status']['id'] !== PaymentEntityService::STATUS_OUTSTANDING) {
            throw new PaymentInvalidStatusException('Invalid payment status: ' . $payment['status']['id']);
        }

        /**
         * 2) Let CPMS know the response from the payment gateway
         *
         * We have to bundle up the response data verbatim as it can
         * vary per gateway implementation
         */
        $client->put('/api/gateway/' . $reference . '/complete', ApiService::SCOPE_CARD, $data);

        // do we need to handle an unexpected response here?

        /**
         * 3) Now actually look up the status of the transaction and
         * update our payment record & fee(s) accordingly
         */
        $params = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];

        $apiResponse = $client->get('/api/payment/' . $reference, ApiService::SCOPE_QUERY_TXN, $params);

        switch ($apiResponse['payment_status']['code']) {
            case self::PAYMENT_SUCCESS:
                foreach ($fees as $fee) {
                    $data = [
                        'feeStatus'      => FeeEntityService::STATUS_PAID,
                        'receivedDate'   => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
                        'receiptNo'      => $reference,
                        'paymentMethod'  => FeePaymentEntityService::METHOD_CARD_OFFLINE,
                        'receivedAmount' => $fee['amount']
                    ];

                    $this->getServiceLocator()
                        ->get('Entity\Fee')
                        ->forceUpdate($fee['id'], $data);

                    $this->getServiceLocator()->get('Listener\Fee')->trigger(
                        $fee['id'],
                        FeeListenerService::EVENT_PAY
                    );
                }

                $paymentService->setStatus($payment['id'], PaymentEntityService::STATUS_PAID);
                $status = PaymentEntityService::STATUS_PAID;
                break;

            case self::PAYMENT_FAILURE:
                $status = PaymentEntityService::STATUS_CANCELLED;
                break;

            case self::PAYMENT_CANCELLATION:
                $status = PaymentEntityService::STATUS_FAILED;
                break;

            default:
                $this->log('Unknown CPMS payment_status: ' . $apiResponse['payment_status']['code']);
                $status = null;
                break;
        }

        if ($status !== null) {
            $paymentService->setStatus($payment['id'], $status);
            return $status;
        }
    }
}
