<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Util\RestClient;

/**
 * Class RefData
 * @package Common\Service
 */
class Country extends AbstractData implements ListData
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
        $data = $this->fetchListData();

        if (!$data) {
            return [];
        }

        if ('isMemberState' == $category) {
            $data = $this->removeNonMemberStates($data);
        }

        return $this->formatData($data);
    }

    public function removeNonMemberStates($data)
    {
        $members = [];

        foreach ($data as $state) {

            if (trim($state['isMemberState']) == 'Y') {

                $members[] = $state;
            }
        }

        return $members;
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @internal param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Country'))) {

            $data = $this->getRestClient()->get('', ['limit' => 1000, 'sort' => 'countryDesc']);

            $this->setData('Country', false);

            if (isset($data['Results'])) {
                $this->setData('Country', $data['Results']);
            }
        }

        return $this->getData('Country');
    }
}
