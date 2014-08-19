<?php

namespace Common\Form\Element;

use Zend\Form\Element\Select;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\RefData as RefDataService;

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
    protected $category;

    /**
     * If set the element will request grouped data from the select service
     *
     * @var boolean
     */
    protected $useGroups = false;

    /**
     * Instance of the ref data service
     *
     * @var RefDataService
     */
    protected $refDataService;



    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['category'])) {
            $this->setCategory($this->options['category']);
        }

        if (isset($this->options['use_groups'])) {
            $this->setUseGroups($this->options['use_groups']);
        }

        return $this;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
     * @param \Common\Service\RefData $refDataService
     * @return $this
     */
    public function setRefDataService(RefDataService $refDataService)
    {
        $this->refDataService = $refDataService;
        return $this;
    }

    /**
     * @return \Common\Service\RefData
     */
    public function getRefDataService()
    {
        return $this->refDataService;
    }

    /**
     * Returns the value options for this select, fetching from the refdata service if requried
     *
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            $refDataService = $this->getRefDataService();
            $this->valueOptions = $refDataService->fetchListOptions($this->getCategory(), $this->useGroups());
        }

        return $this->valueOptions;
    }
}
