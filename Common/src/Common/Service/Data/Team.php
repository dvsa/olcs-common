<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class Team
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Team extends AbstractData implements ListData
{
    protected $serviceName = 'Team';

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
            $optionData[$datum['id']] = $datum['name'];
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
        if (is_null($this->getData('Team'))) {

            $data = $this->getRestClient()->get('', ['limit' => 1000]);

            $this->setData('Team', false);

            if (isset($data['Results'])) {
                $this->setData('Team', $data['Results']);
            }
        }

        return $this->getData('Team');
    }
}
