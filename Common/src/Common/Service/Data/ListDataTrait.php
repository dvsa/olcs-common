<?php

namespace Common\Service\Data;

/**
 * Trait ListDataTrait
 *
 * Provides a default implementation of ListDataInterface requires defining one method for handling fetching data
 *
 * @package Common\Service\Data
 */
trait ListDataTrait
{
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
     * @param $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = $this->fetchListData($context);

        if (!$data) {
            return [];
        }

        if ($useGroups) {
            return $this->formatDataForGroups($data);
        }

        return $this->formatData($data);
    }

    /**
     * Look up a property based on a key with a known value
     *
     * @param string $key
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    private function getPropertyFromKey($key, $property, $value)
    {
        $data = $this->fetchListData([]);
        foreach ($data as $datum) {
            if ($datum[$key] == $value) {
                return $datum[$property];
            }
        }

        return null;
    }

    /**
     * @param $context
     * @return array
     */
    abstract public function fetchListData($context);
}
