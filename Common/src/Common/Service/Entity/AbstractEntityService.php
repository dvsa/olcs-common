<?php

/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractEntityService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity;

    /**
     * Save the entity
     *
     * @param array $data
     */
    public function save($data)
    {
        if (($entity = $this->getEntity()) === null) {
            throw new \Exception('Entity is not defined');
        }

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        } else {
            $method = 'POST';
        }

        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall($entity, $method, $data);
    }

    /**
     * Delete the entity by its ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (($entity = $this->getEntity()) === null) {
            throw new \Exception('Entity is not defined');
        }

        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall($entity, 'DELETE', array('id' => $id));
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
