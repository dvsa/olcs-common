<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Entity\ContactDetailsEntityService;

/**
 * Class Partner
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Partner extends AbstractData implements ListData
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
        $data = $this->fetchListData();

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend
     *
     * @internal param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Partner'))) {

            $params = [
                'limit' => 1000,
                'bundle' => $this->bundle,
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_PARTNER
            ];

            $data = $this->getRestClient()->get('', $params);

            $this->setData('Partner', false);

            if (isset($data['Results'])) {
                $this->setData('Partner', $data['Results']);
            }
        }

        return $this->getData('Partner');
    }
}
