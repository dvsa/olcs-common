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

    /**
     * Get a document's identifier
     *
     * @param int $id
     * @return string
     */
    public function getIdentifier($id)
    {
        $data = $this->get($id);

        return (isset($data['identifier']) && !empty($data['identifier'])) ? $data['identifier'] : null;
    }
}
