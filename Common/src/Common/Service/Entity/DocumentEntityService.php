<?php

/**
 * Document Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\File\File;

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

    public function createFromFile(File $file, $data = [])
    {
        $defaults = [
            'identifier' => $file->getIdentifier(),
            'size'       => $file->getSize(),
            'issuedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        ];

        $data = array_merge($defaults, $data);

        return $this->save($data);
    }
}
