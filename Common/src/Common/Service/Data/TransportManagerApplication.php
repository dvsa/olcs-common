<?php

namespace Common\Service\Data;

use Common\Service\Data\CrudAbstract;
use Common\Service\Data\Interfaces\ListData;
use Common\Util\RestClient;

/**
 * Service Class Task
 *
 * @package Common\Service\Data
 */
class TransportManagerApplication extends AbstractData
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $serviceName = 'TransportManagerApplication';

    /**
     * Fetches a list of Transport Managers by application Id
     * @param integer $applicationId
     * @return array
     */
    public function fetchTmListOptionsByApplicationId($applicationId)
    {
        $optionData = [];
        $data = $this->fetchByApplicationId($applicationId);

        foreach ($data as $datum) {
            $optionData[$datum['transportManager']['id']] = $datum['transportManager']['homeCd']['person']['forename'] .
                ' ' . $datum['transportManager']['homeCd']['person']['familyName'];
        }

        return $optionData;
    }

    public function fetchByApplicationId($applicationId)
    {
        if (is_null($this->getData($this->serviceName . 'Application_' . $applicationId))) {
            $data = $this->fetchList(
                [
                    'bundle' => json_encode($this->getBundle()),
                    'application' => $applicationId
                ]
            );

            $this->setData($this->serviceName . 'Application_' . $applicationId, false);

            if (isset($data['Results'])) {
                $this->setData($this->serviceName . 'Application_' . $applicationId, $data['Results']);
            }
        }

        return $this->getData($this->serviceName . 'Application_' . $applicationId);
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'transportManager' => array(
                    'children' => array(
                        'homeCd' => array(
                            'children' => array(
                                'person'
                            )
                        )
                    )
                )
            )
        );
        return $bundle;
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
