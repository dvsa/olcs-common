<?php

namespace Common\Service\Data;

use Common\Util\RestClient;

/**
 * Class RefData
 * @package Common\Service
 */
class RefData extends AbstractData implements ListDataInterface
{
    protected $serviceName = 'RefData';

    /**
     * @param $data
     * @return array
     */
    public function formatDataForGroups($data)
    {
        $groups = [];
        $optionData = [];

        foreach ($data as $datum) {
            //false if null or not in array
            if (isset($datum['parent']['id'])) {
                $groups[$datum['parent']['id']][] = $datum;
            } else {
                $optionData[$datum['id']] = ['label' => $datum['description'], 'options' => []];
            }
        }

        foreach ($groups as $parent => $groupData) {
            $optionData[$parent]['options'] = $this->formatData($groupData);
        }

        return $optionData;
    }

    /**
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
        $data = $this->fetchListData($category);

        if (!$data) {
            return [];
        }

        if ($useGroups) {
            return $this->formatDataForGroups($data);
        }

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchListData($category)
    {
        if (is_null($this->getData($category))) {
            $data = $this->getRestClient()->get(sprintf('/%s', $category));
            $this->setData($category, $data);
        }

        return $this->getData($category);
    }
}
