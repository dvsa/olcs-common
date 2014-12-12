<?php

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusRegEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'BusReg';

    public function findByIdentifier($identifier)
    {
        $result = $this->get(['regNo' => $identifier]);
        if ($result['Count'] === 0) {
            return false;
        }
        return $result['Results'][0];
    }
}
