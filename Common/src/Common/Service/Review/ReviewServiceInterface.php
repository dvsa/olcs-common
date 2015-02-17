<?php

/**
 * Review Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Review Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ReviewServiceInterface
{
    /**
     *
     * @param array $data
     * @return string
     */
    public function getHeader(array $data = array());

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array());
}
