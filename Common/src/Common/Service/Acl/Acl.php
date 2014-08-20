<?php

/**
 * Acl
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Db\Service\Acl;

use Zend\Permissions\Acl as ZendAcl;

/**
 * Acl
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Acl
{
    /**
     * Used to contain an instance of
     *
     * @var Zend\Permissions\Acl\Acl
     */
    protected $acl = null;

    /**
     * When instantated, we expect an array of hydrated Resource objects.
     */
    public function __construct(array $resources = [])
    {
        if (is_array($resources) && count($resources) > 0) {
            $this->setResources($resources);
        }
    }

    public function setResources(array $resources = [])
    {
        foreach ($resources as $resource) {
            if ($resource instanceof \Olcs\Common\Model\Permission) {
                $resource = new Resource($resource->getHandle());
            }

            $this->setResource($resource);
        }
    }

    /**
     * Sets the resource.
     *
     * @param Resource $resource
     *
     * @return \Olcs\Db\Service\Acl\Acl
     */
    public function setResource(Resource $resource)
    {
        $this->getAcl()->addResource($resource);
        $this->getAcl()->allow('default', $resource);

        return $this;
    }

    /**
     * Allows the injection in of an Acl conforming class.
     *
     * @param Zend\Permissions\Acl\AclInterface $acl
     *
     * @return \Olcs\Db\Service\Acl
     */
    public function setAcl(ZendAcl\AclInterface $acl)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Gets an instantiated instance of Acl\Acl. If there is no Acl set,
     * a logical default is instantiated.
     *
     * @return Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        if (null === $this->acl) {
            $this->acl = new ZendAcl\Acl();
            $this->acl->addRole('default');
        }

        return $this->acl;
    }
}
