<?php

namespace Common\Service\Data\Interfaces;

/**
 * Interface Retrievable
 * @package Common\Service\Data\Interfaces
 */
interface Retrievable
{
    /**
     * Retuns the total count (total unfiltered results) of the data set extracted from a list REST call. Used for
     * pagination where the total results are required.
     * Returns false if data has not been set.
     *
     * @param $key
     * @return integer|bool false if data not set
     */
    public function getCount($key);

    /**
     * Retuns the data set extracted from a list REST call by key.
     * Returns false if data has not been set.
     *
     * @param $key
     * @return integer|bool false if data not set
     */
    public function getResults($key);

    /**
     * Fetch a list of results from backend and return as an array of arrays
     *
     * Caches result locally on this object, if we implement persistent caching, care needs to be taken over different
     * possible parameters passed into this method.
     *
     * @param array $params
     * @param Bundle $bundle
     * @return array
     */
    public function fetchList($params = [], $bundle = null);

}
