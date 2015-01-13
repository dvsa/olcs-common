<?php

/**
 * System Parameter Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * System Parameter Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SystemParameterEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'SystemParameter';

    /**
     * Retrieve a value by its key
     */
    public function getValue($key)
    {
        $row = $this->get($key);
        if ($row === false) {
            return false;
        }
        return $row['paramValue'];
    }
}
