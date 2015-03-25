<?php

/**
 * Generic mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Mapper;

use Common\Data\Object\Bundle;

/**
 * Generic mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class GenericMapper
{

    /**
     * Default fieldMap to use when one isn't passed to the service
     */
    protected $fieldMap = [];

    /**
     * Gets the field map
     *
     * @return array
     */
    public function getFieldMap()
    {
        return $this->fieldMap;
    }

    /**
     * sets the field map
     *
     * @param $fieldMap
     * @return $this
     */
    public function setFieldMap($fieldMap)
    {
        $this->fieldMap = $fieldMap;
        return $this;
    }

    /**
     * Formats data to suit a form that has fieldsets, based on the map.
     * Could improve on this but it's a start
     *
     * @param array $data
     * @param null|array $fieldMap
     * @return array
     */
    public function formatDataForForm($data, $fieldMap = null)
    {
        $formData = [];
        $fieldMap = $fieldMap ?: $this->getFieldMap();

        //first we'll populate the keys that aren't from child entities
        //this avoids us having to write config for these
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $formData[$key] = $value;
                unset($data[$key]);
            }
        }

        foreach ($fieldMap as $fieldKey => $formFieldset) {
            $explodedKey = explode('||', $fieldKey);
            $levelsDeep = count($explodedKey);
            $keyData = null;

            //shouldn't be necessary as these keys wouldn't be needed in config,
            //but makes the code a bit more robust nonetheless
            if ($levelsDeep == 1) {
                continue;
            }

            $keyData = $data[$explodedKey[0]];

            for ($i = 1; $i < $levelsDeep; $i ++) {
                if (isset($keyData[$explodedKey[$i]])) {
                    $keyData = $keyData[$explodedKey[$i]];
                } else {
                    $keyData = null;
                    break;
                }
            }

            if (!is_null($keyData)) {
                $lastKey = end($explodedKey);

                //if the field is an id field, we actually need the previous key
                if ($lastKey == 'id') {
                    $levelsBack = 2;
                } else {
                    $levelsBack = 1;
                }

                $formData[$formFieldset][$explodedKey[$levelsDeep - $levelsBack]] = $keyData;
            }
        }

        return $formData;
    }
}
