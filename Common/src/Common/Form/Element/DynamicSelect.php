<?php

namespace Common\Form\Element;

use Common\Service\Data\ListDataInterface;
use Zend\Form\Element\Select;
use Common\Service\Data\RefData as RefDataService;

/**
 * Class DynamicSelect
 * @package Common\Form\Element
 */
class DynamicSelect extends Select
{
    /**
     * Category of data to fetch for this select box
     *
     * @var string
     */
    protected $context;

    /**
     * If set the element will request grouped data from the select service
     *
     * @var boolean
     */
    protected $useGroups = false;

    /**
     * @var \Common\Service\Data\ListDataInterface
     */
    protected $dataService;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Name of the data service to use to fetch list options from
     *
     * @var RefDataService
     */
    protected $serviceName = 'Common\Service\Data\RefData';


    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['context'])) {
            $this->setContext($this->options['context']);
        } elseif (isset($this->options['category'])) {
            //for bc
            $this->setContext($this->options['category']);
        }

        if (isset($this->options['use_groups'])) {
            $this->setUseGroups($this->options['use_groups']);
        }

        if (isset($this->options['service_name'])) {
            $this->setServiceName($this->options['service_name']);
        }

        return $this;
    }

    /**
     * @param string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param $useGroups
     * @return $this
     */
    public function setUseGroups($useGroups)
    {
        $this->useGroups = (bool) $useGroups;
        return $this;
    }

    /**
     * @return boolean
     */
    public function useGroups()
    {
        return $this->useGroups;
    }

    /**
     * @param \Common\Service\Data\RefData $serviceName
     * @return $this
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     * @return \Common\Service\Data\RefData
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param \Common\Service\Data\ListDataInterface $dataService
     * @return $this
     */
    public function setDataService($dataService)
    {
        $this->dataService = $dataService;
        return $this;
    }

    /**
     * @throws \Exception If service doesn't implement ListDataInterface
     * @return \Common\Service\Data\ListDataInterface
     */
    public function getDataService()
    {
        if (is_null($this->dataService)) {
            $this->dataService = $this->getServiceLocator()->get($this->getServiceName());
            if (!($this->dataService instanceof ListDataInterface)) {
                throw new \Exception(
                    sprintf(
                        'Class %s does not implement \Common\Service\Data\ListDataInterface',
                        $this->getServiceName()
                    )
                );
            }
        }

        return $this->dataService;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Returns the value options for this select, fetching from the refdata service if requried
     *
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            $refDataService = $this->getDataService();
            $this->valueOptions = $refDataService->fetchListOptions($this->getContext(), $this->useGroups());
        }

        return $this->valueOptions;
    }

    /**
     * Sets the value, if an array is passed in with an id key it assumes it's a ref_data entity and sets the value
     * to be equal to the id
     *
     * @param mixed $value
     * @return \Zend\Form\Element
     */
    public function setValue($value)
    {
        if (is_array($value) && array_key_exists('id', $value)) {
            $value = $value['id'];
        }
        return parent::setValue($value);
    }
}
