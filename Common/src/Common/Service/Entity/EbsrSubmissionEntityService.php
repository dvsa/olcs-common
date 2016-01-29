<?php

/**
 * EbsrSubmission Entity Service
 */
namespace Common\Service\Entity;

/**
 * EbsrSubmission Entity Service
 */
class EbsrSubmissionEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'EbsrSubmission';

    /**
     * Main data bundle
     *
     * @var array
     */
    private $mainDataBundle = array(
        'children' => array(
            'document'
        )
    );

    /**
     * Find the most recent Route No by Licence
     * Assumes that Route Numbers are incremental
     *
     * @param $licenceId
     * @return array
     */
    public function fetchByBusRegId($busRegId)
    {
        $params = [
            'busReg' => $busRegId,
        ];

        $result = $this->get($params, $this->mainDataBundle);
        if ($result['Count'] === 0) {
            return false;
        }

        return $result['Results'][0];
    }
}
