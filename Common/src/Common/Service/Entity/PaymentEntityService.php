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

    public function setStatus($id, $status)
    {
        return $this->forceUpdate($id, ['status' => $status]);
    }
}
