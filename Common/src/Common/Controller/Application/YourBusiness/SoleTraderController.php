<?php

/**
 * Sole Trader Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

use Common\Controller\Traits;

/**
 * Sole Trader Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SoleTraderController extends YourBusinessController
{
    use Traits\GenericIndexAction;

    /**
     * Data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data',
            ),
        )
    );

    protected $service = 'Person';

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'title',
            'forename',
            'familyName',
            'birthDate',
            'otherName',
        )
    );

    /**
     * Save data
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        $applicationId = $this->getIdentifier();
        $data['application'] = $applicationId;
        parent::save($data, 'Person');
    }

    /**
     * Load data
     *
     * @param $id
     * @return array
     */
    protected function load($id)
    {
        $data = $this->makeRestCall(
            $this->getService(),
            'GET',
            array('application' => $id),
            $this->getDataBundle()
        );
        return array(
            'data' => count($data['Results']) ? $data['Results'][0] : array()
        );
    }
}
