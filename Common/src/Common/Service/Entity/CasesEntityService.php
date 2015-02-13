<?php

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CasesEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Cases';

    private $identifierBundle = [
        'children' => [
            'licence'
        ]
    ];

    public function findByIdentifier($identifier)
    {
        // a case's identifier is also its primary key...
        return $this->get($identifier, $this->identifierBundle);
    }

    public function getOpenForLicence($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'closeDate' => 'NULL',
            'deletedDate' => 'NULL',
        ];

        //@TODO how do we determine whether a case is 'PI' or not?
        $bundle = [
            'children' => ['publicInquirys']
        ];

        $data = $this->getAll($query, $bundle);

        return $data['Results'];
    }
}
