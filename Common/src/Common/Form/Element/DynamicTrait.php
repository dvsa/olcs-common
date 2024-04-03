<?php

namespace Common\Form\Element;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\PluginManager;
use Psr\Container\ContainerInterface;

trait DynamicTrait
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
     * If set the element will have an extra option "Other"
     *
     * @var boolean
     */
    protected $otherOption = false;

    /**
     * List of options to exclude
     *
     * @var array
     */
    protected $exclude = [];

    protected $dataService;

    protected PluginManager $dataServiceManager;

    protected ContainerInterface $serviceLocator;

    /**
     * Name of the data service to use to fetch list options from
     *
     * @var string
     */
    protected $serviceName = \Common\Service\Data\RefData::class;

    /**
     * Extra options to include in the dropdown
     * Eg 'unassigned' => 'Unassigned", 'not-set' => 'Not set', 'all' =>
     *
     * @var array
     */
    protected $extraOption;


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
    public function otherOption()
    {
        return $this->otherOption;
    }

    /**
     * @param $otherOption
     * @return $this
     */
    public function setOtherOption($otherOption)
    {
        $this->otherOption = (bool) $otherOption;
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
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function setDataService($dataService)
    {
        $this->dataService = $dataService;
        return $this;
    }

    /**
     * @throws \Exception If service doesn't implement ListData
     * @return \Common\Service\Data\Interfaces\ListData
     */
    public function getDataService()
    {
        if (is_null($this->dataService)) {
            $this->dataService = $this->dataServiceManager->get($this->getServiceName());
            if (!($this->dataService instanceof ListData)) {
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

    public function getServiceLocator(): ContainerInterface
    {
        return $this->serviceLocator;
    }

    /**
     * @return $this
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }

    /**
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * Get extra options
     *
     * @return mixed
     */
    public function getExtraOption()
    {
        return $this->extraOption;
    }

    /**
     * Set extra options to appear in the drop down (eg 'unassigned' => 'Unassigned')
     *
     * @param array $extraOption
     */
    public function setExtraOption($extraOption): void
    {
        $this->extraOption = $extraOption;
    }

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

        if (isset($this->options['other_option'])) {
            $this->setOtherOption($this->options['other_option']);
        }

        if (isset($this->options['exclude'])) {
            $this->setExclude($this->options['exclude']);
        }

        if (isset($this->options['extra_option'])) {
            $this->setExtraOption($this->options['extra_option']);
        }

        return $this;
    }

    /**
     * Returns the value options for this select, fetching from the refdata service if requried
     */
    public function getValueOptions(): array
    {
        if (empty($this->valueOptions)) {
            $refDataService = $this->getDataService();
            $this->valueOptions = $refDataService->fetchListOptions($this->getContext(), $this->useGroups());

            if (!empty($this->extraOption)) {
                $this->valueOptions = $this->extraOption + $this->valueOptions;
            }
        }

        if (!empty($this->getExclude())) {
            // exclude unwanted options
            $this->valueOptions = array_diff_key($this->valueOptions, array_flip($this->getExclude()));
        }

        if ($this->otherOption()) {
            $this->valueOptions['other'] = 'Other';
        }

        return $this->valueOptions;
    }

    /**
     * Sets the value, if an array is passed in with an id key it assumes it's a ref_data entity and sets the value
     * to be equal to the id
     *
     * @param mixed $value
     * @return \Laminas\Form\Element
     */
    public function setValue($value)
    {
        if ($value === []) {
            $value = null;
        } elseif (is_array($value) && array_key_exists('id', $value)) {
            $value = $value['id'];
        } elseif ($this->getAttribute('multiple') && is_array($value)) {
            $tmp = [];
            foreach ($value as $singleValue) {
                $tmp[] = is_array($singleValue) && array_key_exists('id', $singleValue) ? $singleValue['id'] : $singleValue;
            }

            $value = $tmp;
        }

        return parent::setValue($value);
    }

    public function addValueOption(array $valueOption): void
    {
        $this->setValueOptions(array_merge($this->getValueOptions(), $valueOption));
    }
}
