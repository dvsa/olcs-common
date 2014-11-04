<?php

namespace Common\Service\Data;

use Common\Util\RestClient, Common\Service\Data\AbstractData;

/**
 * Interface CrudInterface
 *
 * @package Common\Service\Data
 */
abstract class CrudAbstract extends AbstractData implements CrudInterface
{
    protected $serviceName = '';

    /**
     * Gets a single record.
     */
    public function get($id)
    {
        return $this->getRestClient()->get('', ['id' => $id]);
    }

    /**
     * Updates a record.
     *
     * @internal array $data
     *
     * @return integer
     */
    public function update(array $data)
    {
        return $this->getRestClient()->{__FUNCTION__}('', $data);
    }

    /**
     * Adds a record to this service and returns the ID.
     *
     * @internal array $data
     *
     * @return integer
     */
    public function create(array $data)
    {
        return $this->getRestClient()->{__FUNCTION__}('', $data);
    }

    /**
     * Deletes a single record.
     */
    public function delete($id)
    {
        return $this->getRestClient()->delete('', ['id' => $id]);
    }
}
