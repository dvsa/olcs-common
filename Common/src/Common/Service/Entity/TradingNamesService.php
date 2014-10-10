<?php

/**
 * Trading Names Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Trading Names Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TradingNamesService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TradingNames';

    public function save($data)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'POST', $data);
    }
}
