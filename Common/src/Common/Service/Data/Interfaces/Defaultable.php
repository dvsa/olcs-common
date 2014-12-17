<?php

namespace Common\Service\Data\Interfaces;

use Common\Data\Object\Bundle;

/**
 * Interface Defaultable
 * @package Common\Service\Data\Interfaces
 */
interface Defaultable extends DataService
{
    /**
     * Sets the default ID to use when retrieving a single item from the backend
     *
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * Fetch one result from backend and return as an array, if $id isn't specified it must use the id previously set
     * via setId
     *
     * @param null $id
     * @param Bundle $bundle
     * @return mixed
     */
    public function fetchOne($id = null, $bundle = null);
}
