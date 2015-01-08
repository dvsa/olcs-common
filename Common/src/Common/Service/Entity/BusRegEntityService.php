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
     * Main data bundle
     *
     * @var array
     */
    private $mainDataBundle = array(
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
        return $this->get($id, $this->mainDataBundle);
    }

    public function findByIdentifier($identifier)
    {
        /**
         * @TODO this AC still isn't correct; the logic to fetch
         * the currently active variation (as opposed to highest)
         * will be fulfilled by https://jira.i-env.net/browse/OLCS-6334
         *
         * That story is expected to implement a custom backend service which
         * *should* satisfy all calls made to the BusReg service, but this
         * method will most likely still need revisiting and thus get its
         * own story in the future
         */
        $params = [
            'regNo' => $identifier,
            'sort'  => 'variationNo',
            'order' => 'DESC'
        ];

        $result = $this->get($params, $this->mainDataBundle);
        if ($result['Count'] === 0) {
            return false;
        }

        return $result['Results'][0];
    }
}
