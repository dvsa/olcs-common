<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class LocalAuthority
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LocalAuthority extends AbstractData implements ListData
{
    protected $serviceName = 'LocalAuthority';

    protected $bundle = [
        'children' => [
            'trafficArea'
        ]
    ];

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
     * Format for groups
     *
     * @param array $data
     * @return array
     */
    public function formatDataForGroups(array $data)
    {
        $optionData = [];

        foreach ($data as $datum) {
            $taId = $datum['txcName'];
            if (!isset($optionData[$taId])) {
                $optionData[$taId] = [
                    'label' => $datum['trafficArea']['name'],
                    'options' => []
                ];
            }
            $optionData[$taId]['options'][$datum['id']] = $datum['description'];
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

        if ($useGroups) {
            return $this->formatDataForGroups($data);
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
        if (is_null($this->getData('LocalAuthority'))) {

            $params = [
                'limit' => 1000,
                'bundle' => json_encode($this->bundle)
            ];

            $data = $this->getRestClient()->get('', $params);

            $this->setData('LocalAuthority', false);

            if (isset($data['Results'])) {
                $this->setData('LocalAuthority', $data['Results']);
            }
        }

        return $this->getData('LocalAuthority');
    }
}
