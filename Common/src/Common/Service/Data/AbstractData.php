<?php

namespace Common\Service\Data;

use Common\Exception\BadRequestException;
use Common\Util\RestClient;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Stdlib\ArrayObject;

/**
 * Class AbstractData
 * @package Olcs\Service\Data
 */
abstract class AbstractData implements FactoryInterface, RestClientAwareInterface
{
    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param \Common\Util\RestClient $restClient
     * @return $this
     */
    public function setRestClient(RestClient $restClient)
    {
        $this->restClient = $restClient;
        return $this;
    }

    /**
     * @return \Common\Util\RestClient
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param $key
     * @param $data
     * @return $this
     */
    public function setData($key, $data)
    {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @return \Zend\Stdlib\ArrayObject
     */
    public function createEmpty()
    {
        return new ArrayObject();
    }

    /**
     * Creates a data object
     *
     * @param $data
     * @return \Zend\Stdlib\ArrayObject
     */
    public function createWithData($data)
    {
        $dataObject = $this->createEmpty();

        foreach ($data as $key => $value) {
            $dataObject->offsetSet($key, $value);
        }

        return $dataObject;
    }

    /**
     * @param array $params
     * @param null $bundle
     * @return mixed
     */
    public function fetchList($params = [], $bundle = null)
    {
        $params['bundle'] = json_encode(empty($bundle) ? $this->getBundle() : $bundle);
        return $this->getRestClient()->get($params);
    }

    /**
     * @param \Zend\Stdlib\ArrayObject $dataObject
     * @param string $service
     * @return mixed
     */
    public function createFromObject(ArrayObject $dataObject, $service)
    {
        $dataObject = $this->getServiceLocator()->get($service)->filter($dataObject);
        return $this->save($dataObject);
    }

    /**
     * @param \Zend\Stdlib\ArrayObject $dataObject
     * @return mixed
     */
    public function save(ArrayObject $dataObject)
    {
        $params = ['data' => json_encode($dataObject->getArrayCopy())];
        $id = $dataObject->offsetGet('id');

        if ($id) {
            $return = $this->getRestClient()->update('/' . $id, $params);
            if (is_array($return)) {
                return $id;
            }
        } else {
            $return = $this->getRestClient()->create('', $params);

            if (isset($return['id'])) {
                return $return['id'];
            }
        }

        return false;
    }

    /**
     * Deletes a record based on the id
     *
     * @param $id
     * @return bool
     * @throws BadRequestException
     */
    public function delete($id)
    {
        $delete = $this->getRestClient()->delete($id);

        //rest client returns empty array on success
        if (!is_array($delete)) {
            throw new BadRequestException('Record could not be deleted');
        }

        return true;
    }

    /**
     * Deletes data based on a set of params
     *
     * @param $params
     * @throws BadRequestException
     */
    public function deleteList($params)
    {
        $data = $this->fetchList($params);

        foreach ($data['Results'] as $record) {
            $this->delete($record['id']);
        }
    }

    /**
     * @return array
     */
    protected function getBundle()
    {
        return [
            'properties' => 'ALL',
        ];
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @deprecated
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Common\Util\ResolveApi $apiResolver */
        $apiResolver = $serviceLocator->get('ServiceApiResolver');
        /** @var \Zend\Mvc\I18n\Translator $translator */
        $translator = $serviceLocator->get('translator');

        $client = $apiResolver->getClient($this->getServiceName());
        $client->setLanguage($translator->getLocale());
        $this->setRestClient($client);

        return $this;
    }
}
