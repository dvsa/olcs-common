<?php

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CasesEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Cases';

    public function findByIdentifier($identifier)
    {
        // a case's identifier is also its primary key
        return $this->get($identifier);
    }
}
