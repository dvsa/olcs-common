<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Util\RestClient;

/**
 * Class RefData
 * @package Common\Service
 */
class SiCategoryType extends AbstractData implements ListData
{
    protected $serviceName = 'SiCategoryType';

    /**
     * Format data!
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data)
    {
        $optionData = [];

        foreach ($data as $datum) {
            $optionData[$datum['id']] = $datum['description'];
        }

        return $optionData;
    }

    /**
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($category, $useGroups = false)
    {
        $data = $this->fetchListData();

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
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

            $data = $this->getRestClient()->get('', ['limit' => 1000, 'sort' => 'description']);

            $this->setData($this->serviceName, false);

            if (isset($data['Results'])) {
                $this->setData($this->serviceName, $data['Results']);
            }
        }

        return $this->getData($this->serviceName);
    }
}
