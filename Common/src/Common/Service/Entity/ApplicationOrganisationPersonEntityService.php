<?php

/**
 * Application Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Application Organisation Person Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOrganisationPersonEntityService extends AbstractEntityService
{
    /**
     * Set a sane limit when fetching people
     */
    const ORG_PERSON_LIMIT = 50;

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ApplicationOrganisationPerson';

    /**
     * Holds the organisation people
     *
     * @var array
     */
    private $peopleBundle = array(
        'children' => array(
            'person'
        )
    );

    /**
     * Get all people for a given application
     *
     * @param int $applicationId
     * @param int $limit
     */
    public function getAllByApplication($applicationId, $limit = null)
    {
        if ($limit === null) {
            $limit = self::ORG_PERSON_LIMIT;
        }

        $query = array(
            'organisation' => $applicationId,
            'limit' => $limit
        );

        return $this->get($query, $this->peopleBundle);
    }
}
