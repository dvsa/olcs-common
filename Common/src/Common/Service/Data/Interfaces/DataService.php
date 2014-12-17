<?php

namespace Common\Service\Data\Interfaces;

use Common\Data\Object\Bundle;

/**
 * Interface DataService
 * @package Common\Service\Data\Interfaces
 */
interface DataService
{
    /**
     * A setter for the default bundle, used when one isn't explicitly passed
     *
     * @param Bundle $bundle
     * @return DataService
     */
    public function setDefaultBundle(Bundle $bundle);

    /**
     * Fetch one result from backend and return as an array
     *
     * @param $id
     * @param Bundle $bundle
     * @return array
     */
    public function fetchOne($id, $bundle = null);

    /**
     * Fetch a list of results from backend and return as an array of arrays.
     *
     * @param array $params
     * @param Bundle $bundle
     * @return array
     */
    public function fetchList($params = [], $bundle = null);
}