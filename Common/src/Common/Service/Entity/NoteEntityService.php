<?php

/**
 * Note Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * Note Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class NoteEntityService extends AbstractEntityService
{
    protected $entity = 'Note';

    protected $bundle = [
        'children' => [
            'createdBy',
            'noteType',
        ]
    ];

    /**
     * @param array $filters filter/query data
     * @return array
     */
    public function getNotesList($filters)
    {
        return $this->get($filters, $this->bundle);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getNote($id)
    {
        return $this->get($id, $this->bundle);
    }
}
