<?php

/**
 * Document Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Document Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Document';

    protected $identifierBundle = array(
        'properties' => array('identifier')
    );

    /**
     * Get a documents identifier
     *
     * @param int $id
     * @return string
     */
    public function getIdentifier($id)
    {
        $data = $this->get($id, $this->identifierBundle);

        return (isset($data['identifier']) && !empty($data['identifier'])) ? $data['identifier'] : null;
    }
}
