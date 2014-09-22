<?php

namespace Common\Service\Data;

use Common\Util\RestClient;

/**
 * Class RefData
 * @package Common\Service
 */
class Country extends AbstractData implements ListDataInterface
{
    protected $serviceName = 'Country';

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
            $optionData[$datum['id']] = $datum['countryDesc'];
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

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Country'))) {

            $data = $this->getRestClient()->get('', ['limit' => 1000]);

            $this->setData('Country', false);

            if (isset($data['Results'])) {
                $this->setData('Country', $data['Results']);
            }
        }

        return $this->getData('Country');
    }
}
