<?php

/**
 * Data Map Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Data Map Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DataMapHelperService extends AbstractHelperService
{
    /**
     * Process the data map
     *
     * @param type $data
     */
    public function processDataMap($oldData, $map = array(), $section = 'main')
    {
        if (empty($map)) {
            return $oldData;
        }

        if (isset($map['_addresses'])) {

            foreach ($map['_addresses'] as $address) {
                $oldData = $this->processAddressData($oldData, $address);
            }
        }

        $data = array();

        if (isset($map[$section]['mapFrom'])) {

            foreach ($map[$section]['mapFrom'] as $key) {

                if (isset($oldData[$key])) {
                    $data = array_merge($data, $oldData[$key]);
                }
            }
        }

        if (isset($map[$section]['children'])) {

            foreach ($map[$section]['children'] as $child => $options) {
                $data[$child] = $this->processDataMap($oldData, array($child => $options), $child);
            }
        }

        if (isset($map[$section]['values'])) {
            $data = array_merge($data, $map[$section]['values']);
        }

        return $data;
    }

    /**
     * Find the address fields and process them accordingly
     *
     * @param array $data
     * @return array $data
     */
    private function processAddressData($data, $addressName = 'address')
    {
        if (!isset($data['addresses'])) {
            $data['addresses'] = array();
        }

        unset($data[$addressName]['searchPostcode']);

        $data['addresses'][$addressName] = $data[$addressName];

        unset($data[$addressName]);

        return $data;
    }
}
