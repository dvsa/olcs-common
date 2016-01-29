<?php

namespace Common\Service\Data;

use Common\Service\Data\Generic as GenericService;

/**
 * Service Class Trait
 *
 * @package Common\Service\Data
 */
trait GenericAwareTrait
{
    /**
     * @var GenericService
     */
    private $genericService;

    /**
     * @param GenericService $genericService
     */
    public function setGenericService(GenericService $genericService)
    {
        $this->genericService = $genericService;
    }

    /**
     * @return GenericService
     */
    public function getGenericService()
    {
        return $this->genericService;
    }
}
