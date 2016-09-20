<?php

namespace Common\Service\Data;

/**
 * Trait ListDataTrait
 *
 * Provides a default implementation of ListData requires defining one method for handling fetching data
 *
 * @package Common\Service\Data
 */
trait ListDataTrait
{
    /**
     * Format data for groups
     *
     * @param array $data Data
     *
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
     * Format data
     *
     * @param array $data Data
     *
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
     * Fetch list options
     *
     * @param array|string $context   Context
     * @param bool         $useGroups Use groups
     *
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
     * @param string $key      Key
     * @param string $property Property
     * @param mixed  $value    Value
     *
     * @return mixed|null
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
     * Fetch list data
     *
     * @param array|string $context Context
     *
     * @return array
     */
    abstract public function fetchListData($context);
}
