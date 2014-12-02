<?php

namespace Common\Service\Data;

use Common\Util\RestClient;
use Common\Service\Data\AbstractData;

/**
 * Interface CrudInterface
 *
 * @package Common\Service\Data.
 * @deprecated
 */
abstract class CrudAbstract extends AbstractData implements CrudInterface
{
    protected $serviceName = '';

    /**
     * Gets a single record.
     */
    public function get($id)
    {
        if (!$this->getData($id)) {

            $data = $this->getRestClient()->get('', ['id' => $id]);

            if ($data) {
                $this->setData($id, $data);
            }
        }

        return $this->getData($id);
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
        return $this->getRestClient()->{__FUNCTION__}('', array('data' => json_encode($data)));
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
        return $this->getRestClient()->{__FUNCTION__}('', array('data' => json_encode($data)));
    }

    /**
     * Deletes a single record.
     */
    public function delete($id)
    {
        return $this->getRestClient()->delete('', ['id' => $id]);
    }
}
