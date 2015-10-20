<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class Role
 * @package Common\Service
 */
class Role extends AbstractData implements ListData
{
    protected $serviceName = 'Role';

    /**
     * @param $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $optionData = [];
        $data = $this->fetchListData();

        foreach ($data as $datum) {
            $optionData[$datum['role']] = $datum['description'];
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

            $data = $this->getRestClient()->get('', ['limit' => 1000,]);

            $this->setData($this->serviceName, false);

            if (isset($data['Results'])) {
                $this->setData($this->serviceName, $data['Results']);
            }
        }

        return $this->getData($this->serviceName);
    }
}
