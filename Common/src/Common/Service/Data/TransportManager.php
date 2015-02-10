<?php

namespace Common\Service\Data;

use Common\Service\Data\CrudAbstract;

/**
 * Service Class Task
 *
 * @package Common\Service\Data
 */
class TransportManager extends AbstractData
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
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'workCd' => array(
                    'children' => array(
                        'person',
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
