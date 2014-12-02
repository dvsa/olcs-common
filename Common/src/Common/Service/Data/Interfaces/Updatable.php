<?php

namespace Common\Service\Data\Interfaces;

/**
 * Interface Updatable
 * @package Common\Service\Data\Interfaces
 */
interface Updatable
{
    /**
     * Saves data to the backend
     *
     * !! subject to change !!
     *
     * @param array $data
     * @return Updatable
     */
    public function save(array $data);
}