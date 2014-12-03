<?php

/**
 * Fee Payment Helper Service
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

    public function initiateRequest($customerReference, $salesReference, $redirectUrl, array $fees)
    {
        $amount = array_reduce(
            $fees,
            function ($carry, $item) {
                $carry += $item['amount'];
                return $carry;
            }
        );

        $client = $this->getServiceLocator()->get('cpms\service\api');

        // @TODO product ref shouldn't have to come from a whitelist...
        $productReference = 'GVR_APPLICATION_FEE';

        $params = [
            // @NOTE CPMS rejects ints as 'missing', so we have to force a string...
            'customer_reference' => (string)$customerReference,
            'sales_reference' => $salesReference,
            'product_reference' => $productReference,
            'scope' => 'CARD',
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

        $response = $client->post('/api/payment/card', 'CARD', $params);

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
        $apiResponse = $client->put(
            '/api/gateway/' . $reference . '/complete',
            'CARD',
            $data
        );

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

        $apiResponse = $client->get('/api/payment/' . $reference, 'QUERY_TXN', $params);

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
