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
class TransportManager extends AbstractData implements ListData
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $serviceName = 'TransportManager';

    /**
     * @param integer|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchTmData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData($id))) {
            $bundle = is_null($bundle) ? $this->getBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * Fetches a list of Transport Managers by application Id
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($category, $useGroups = false)
    {
        $optionData = [];
        $data = $this->fetchListData();

        foreach ($data as $datum) {
            $optionData[$datum['id']] = $datum['homeCd']['person']['forename'] .
                ' ' . $datum['homeCd']['person']['familyName'];
        }

        return $optionData;
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @internal param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData($this->serviceName))) {

            $data = $this->getRestClient()->get('', ['bundle' => json_encode($this->getBundle()), 'limit' => 100]);

            $this->setData($this->serviceName, false);

            if (isset($data['Results'])) {
                $this->setData($this->serviceName, $data['Results']);
            }
        }

        return $this->getData($this->serviceName);
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'homeCd' => array(
                    'children' => array(
                        'person' => [
                            'children' => [
                                'title'
                            ]
                        ],
                        'address'
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
