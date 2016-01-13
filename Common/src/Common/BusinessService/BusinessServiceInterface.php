<?php

/**
 * Business Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

/**
 * Business Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface BusinessServiceInterface
{
    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return ResponseInterface
     */
    public function process(array $params);
}
