<?php

/**
 * Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PaymentEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Payment';

    const STATUS_OUTSTANDING = 'payment_outstanding';
    const STATUS_CANCELLED = 'payment_cancelled';
    const STATUS_FAILED = 'payment_failed';
    const STATUS_PAID = 'payment_paid';

    protected $detailsBundle = [
        'children' => [
            'status'
        ]
    ];

    public function getDetails($reference)
    {
        $query = [
            'guid' => $reference,
            'limit' => 1
        ];
        $result = $this->get($query, $this->detailsBundle);
        return $result['Count'] === 1 ? $result['Results'][0] : false;
    }
}
