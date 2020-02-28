<?php

/**
 * Fee Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

use Common\RefData;

/**
 * Fee Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeePaymentEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'FeePayment';

    protected $feeBundle = [
        'children' => [
            'fee' => [
                'children' => [
                    'feeStatus',
                    'licence' => [
                        'children' => [
                            'organisation',
                        ],
                    ],
                ],
            ],
        ]
    ];

    /**
     * Helper function to check whether payment type is one of the defined values
     *
     * @param string $value value to test
     * @return boolean
     */
    public function isValidPaymentType($value)
    {
        return in_array(
            $value,
            [
                RefData::FEE_PAYMENT_METHOD_CARD_OFFLINE,
                RefData::FEE_PAYMENT_METHOD_CARD_ONLINE,
                RefData::FEE_PAYMENT_METHOD_CASH,
                RefData::FEE_PAYMENT_METHOD_CHEQUE,
                RefData::FEE_PAYMENT_METHOD_POSTAL_ORDER,
                RefData::FEE_PAYMENT_METHOD_WAIVE,
            ]
        );
    }

    public function getFeesByPaymentId($paymentId)
    {
        $query = array(
            'payment' => $paymentId,
        );

        $data = $this->get($query, $this->feeBundle);

        $fees = [];
        foreach ($data['Results'] as $feePayment) {
            $fees[] = $feePayment['fee'];
        }

        return $fees;
    }
}
