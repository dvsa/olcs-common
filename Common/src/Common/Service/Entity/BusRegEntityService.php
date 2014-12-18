<?php

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * BusReg Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusRegEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'BusReg';

    /**
     * Task data bundle
     *
     * @var array
     */
    private $taskDataBundle = array(
        'children' => array(
            'licence'
        )
    );

    /**
     * Get data for task processing
     *
     * @param int $id
     * @return array
     */
    public function getDataForTasks($id)
    {
        return $this->get($id, $this->taskDataBundle);
    }

    public function findByIdentifier($identifier)
    {
        $result = $this->get(['regNo' => $identifier]);
        if ($result['Count'] === 0) {
            return false;
        }
        return $result['Results'][0];
    }
}
