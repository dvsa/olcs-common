<?php

namespace Common\Service\Data;

use Common\Data\Object\Bundle;
use Common\Exception\BadRequestException;
use Common\Service\Data\Interfaces;

/**
 * Class Generic
 * @package Common\Service\Data
 */
class Generic extends AbstractData implements
    Interfaces\DataService,
    Interfaces\Updatable,
    Interfaces\Deletable,
    Interfaces\BundleAware
{
    /**
     * Default bundle to use when one isn't passed to the service
     *
     * @var Bundle
     */
    protected $bundle;

    /**
     * Initialises the default bundle with an empty one.
     */
    public function __construct()
    {
        $this->bundle = new Bundle();
    }

    /**
     * @param $name
     * @return $this
     */
    public function setServiceName($name)
    {
        $this->serviceName = $name;
        return $this;
    }

    /**
     * Should return the name of the default bundle that this service uses, this can then be retrieved from the bundle
     * manager and injected into the class.
     *
     * @return string
     */
    public function getDefaultBundleName()
    {
        return $this->getServiceName();
    }

    /**
     * A setter for the default bundle, used when one isn't explicitly passed
     *
     * @param Bundle $bundle
     * @return Interfaces\DataService
     */
    public function setDefaultBundle(Bundle $bundle)
    {
        $this->bundle = $bundle;
        return $this;
    }

    /**
     * Protected as this isn't part of the interface and nothing outside of this class needs to know about the
     * bundle being used
     *
     * @return Bundle
     */
    protected function getBundle()
    {
        return $this->bundle;
    }

    /**
     * Fetch one result from backend and return as an array
     *
     * Caches result locally on this object, if we implement persistent caching, care needs to be taken over different
     * possible parameters passed into this method.
     *
     * @param $id
     * @param Bundle $bundle
     * @return array
     */
    public function fetchOne($id, $bundle = null)
    {
        $bundle = $bundle ?: $this->getBundle();
        if ($this->getData($id) === null) {
            $data = $this->getRestClient()->get($this->buildPath($id), ['bundle' => json_encode($bundle)]);
            $this->setData($id, $data);
        }

        return $this->getData($id);
    }

    /**
     * Fetch a list of results from backend and return as an array of arrays.
     *
     * Caches result locally on this object, if we implement persistent caching, care needs to be taken over different
     * possible parameters passed into this method.
     *
     * @param array $params
     * @param Bundle $bundle
     * @return array
     */
    public function fetchList($params = [], $bundle = null)
    {
        if ($this->getData('list') === null) {

            $this->setData('list', false);
            $params['bundle'] = $bundle ?: $this->getBundle();
            $data = $this->getRestClient()->get('', $params);

            if (isset($data['Results'])) {
                $this->setData('list', $data['Results']);
            }
        }

        return $this->getData('list');
    }

    /**
     * Saves data to the backend
     *
     * @param array $data
     * @throws \Common\Exception\BadRequestException
     * @return int
     */
    public function save($data)
    {
        if (isset($data['id'])) {
            $result = $this->getRestClient()->put($this->buildPath($data['id']), ['data' => json_encode($data)]);

            if ($result === []) {
                $result['id'] = $data['id'];
            }
        } else {
            $result = $this->getRestClient()->post('', ['data' => json_encode($data)]);
        }

        if ($result === false) {
            throw new BadRequestException('Record could not be saved');
        }

        $this->setData($result['id'], $result);

        return $result['id'];
    }

    /**
     * Deletes a record
     *
     * @param $id
     * @return bool
     * @throws \Common\Exception\BadRequestException
     */
    public function delete($id)
    {
        $result = $this->getRestClient()->delete($this->buildPath($id));

        if ($result === false) {
            throw new BadRequestException('Record could not be deleted');
        }

        return true;
    }

    /**
     * @param $id
     * @return string
     */
    protected function buildPath($id)
    {
        return sprintf('/%d', $id);
    }
}
