<?php

namespace Common\Data\Mapper\Permits;

use Common\RefData;
use RuntimeException;

/**
 * No of permits mapper
 */
class NoOfPermits
{
    /** @var array */
    private $mappings = [];

    /**
     * @param array $data
     * @param mixed $form
     * @param string $irhpApplicationDataKey
     * @param string $maxPermitsByStockDataKey
     * @param string $feePerPermitDataKey
     *
     * @return array
     *
     * @throws RuntimeException
     */
    public function mapForFormOptions(
        array $data,
        $form,
        $irhpApplicationDataKey,
        $maxPermitsByStockDataKey,
        $feePerPermitDataKey
    ) {
        $permitTypeId = $data[$irhpApplicationDataKey]['irhpPermitType']['id'];

        if (!isset($this->mappings[$permitTypeId])) {
            throw new RuntimeException('Unsupported permit type ' . $permitTypeId);
        }

        $mapper = $this->mappings[$permitTypeId];

        return $mapper->mapForFormOptions(
            $data,
            $form,
            $irhpApplicationDataKey,
            $maxPermitsByStockDataKey,
            $feePerPermitDataKey
        );
    }

    /**
     * Register a mapper class against a permit type
     *
     * @param int $permitTypeId
     * @param mixed $mapper
     */
    public function registerMapper($permitTypeId, $mapper)
    {
        $this->mappings[$permitTypeId] = $mapper;
    }
}
