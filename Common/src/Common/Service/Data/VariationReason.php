<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class VariationReason
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class VariationReason extends AbstractData implements ListData
{
    protected $serviceName = 'VariationReason';

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
        $params = [];

        $data = $this->fetchListData($params);

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
    public function fetchListData($params)
    {
        if (is_null($this->getData('VariationReason'))) {

            $data = $this->getRestClient()->get('', $params);

            $this->setData('VariationReason', false);

            if (isset($data['Results'])) {
                $this->setData('VariationReason', $data['Results']);
            }
        }

        return $this->getData('VariationReason');
    }

    public function fetchById($id)
    {
        return $this->getRestClient()->get('/'.$id, ['bundle' => json_encode($this->getBundle())]);
    }
}
