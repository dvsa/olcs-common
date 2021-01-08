<?php

namespace Common\Service\Qa\DataTransformer;

use RuntimeException;

class EcmtNoOfPermitsSingleDataTransformer implements DataTransformerInterface
{
    const ERR_UNEXPECTED_DATA = 'Unexpected post data content: contains permitsRequired and euro5/euro6';
    const PERMITS_REQUIRED_KEY = 'permitsRequired';
    const EMISSIONS_CATEGORY_KEY = 'emissionsCategory';
    const EURO5_KEY = 'euro5';
    const EURO6_KEY = 'euro6';

    /**
     * {@inheritdoc}
     */
    public function getTransformed(array $data)
    {
        if (!isset($data[self::PERMITS_REQUIRED_KEY])) {
            return $data;
        }

        if (isset($data[self::EURO5_KEY]) || isset($data[self::EURO6_KEY])) {
            throw new RuntimeException(self::ERR_UNEXPECTED_DATA);
        }

        $permitsRequired = $data[self::PERMITS_REQUIRED_KEY];
        $emissionsCategory = $data[self::EMISSIONS_CATEGORY_KEY];

        $data[self::EURO5_KEY] = '0';
        $data[self::EURO6_KEY] = '0';
        $data[$emissionsCategory] = $permitsRequired;

        unset($data[self::PERMITS_REQUIRED_KEY]);
        unset($data[self::EMISSIONS_CATEGORY_KEY]);

        return $data;
    }
}
