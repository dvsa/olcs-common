<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class ContactDetails
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ContactDetails extends AbstractData implements ListData
{
    protected $serviceName = 'ContactDetails';

    protected $bundle = [];

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
        $data = $this->fetchListData($category);

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend
     *
     * @param param $category
     * @return array
     */
    public function fetchListData($category)
    {
        if (is_null($this->getData('ContactDetails'))) {

            $params = [
                'limit' => 1000,
                'bundle' => $this->bundle,
                'contactType' => $category
            ];

            $data = $this->getRestClient()->get('', $params);

            $this->setData('ContactDetails', false);

            if (isset($data['Results'])) {
                $this->setData('ContactDetails', $data['Results']);
            }
        }

        return $this->getData('ContactDetails');
    }
}
