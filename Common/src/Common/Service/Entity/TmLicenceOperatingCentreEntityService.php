<?php

/**
 * Transport Manager Licence Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Licence Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmLicenceOperatingCentreEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TmLicenceOc';

    protected $dataBundle = [
        'children' => [
            'transportManagerLicence',
            'operatingCentre'
        ]
    ];

    /**
     * Delete by transport manager licence id
     *
     * @param int $id
     */
    public function deleteByTmLicence($id)
    {
        $query = [
            'transportManagerLicence' => $id,
        ];
        $this->deleteList($query);
    }
}
