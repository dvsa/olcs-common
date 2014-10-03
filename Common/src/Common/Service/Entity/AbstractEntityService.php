<?php

/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Util;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;


/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractEntityService implements ServiceLocatorAwareInterface
{
    use Util\HelperServiceAware,
        Util\EntityServiceAware,
        ServiceLocatorAwareTrait;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity;

    /**
     * Create a licence
     *
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        if (($entity = $this->getEntity()) === null) {
            throw new \Exception('Entity is not defined');
        }

        return $this->getHelperService('RestHelper')->makeRestCall($entity, 'POST', $data);
    }

    /**
     * Get the defined entity name
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
