<?php

/**
 * Creates a form from a form config file store in
 */

namespace Common\Service\Form;

use Zend\Form\Factory;
use Zend\Form\Element;
use Common\Exception\RuntimeException;

class OlcsCustomFormFactory extends Factory
{

    private $config;

    private $elementsWithValueOptions = array('select', 'multicheckbox', 'radio');

    public $baseFormConfig;

    public $formsPath;
    public $fieldsetPath;

    public function __construct($config)
    {
        $this->config = $config;
        $this->formsPath = $this->config['forms_path'];
        $this->fieldsetsPath = $this->config['fieldsets_path'];
        parent::__construct();
    }

    public function createForm($type)
    {
        if (empty($this->baseFormConfig)) {
            $this->baseFormConfig = $this->getFormConfig($type);
        }
        $formConfig = $this->createFormConfig($this->baseFormConfig[$type]);
        return parent::createForm($formConfig);
    }

    public function getFormConfig($type)
    {
        $path = __DIR__ . $this->formsPath . $type . '.form.php';
        if (!file_exists($path)) {
            throw new \Exception("Form $type has no specification config!");
        }
        $formConfig = include $path;
        return $formConfig;
    }

    public function setFormConfig(array $config)
    {
        $this->baseFormConfig = $config;
    }

    private function createFormConfig($formConfig)
    {
        if (isset($formConfig['fieldsets'])) {
            $formConfig['fieldsets'] = $this->getFieldsets($formConfig['fieldsets']);
        }

        if (isset($formConfig['elements'])) {
            $formConfig['elements'] = $this->getElements($formConfig['elements']);
        }

        return $formConfig;
    }

    private function getElements($elements)
    {
        $thisElements = [];
        foreach ($elements as $key => $element) {

            $element['name'] = isset($element['name']) ? $element['name'] : $key;
            $thisElements[] = $this->getElement($element);
        }
        return $thisElements;
    }

    private function getElement($element, $fieldset = false)
    {
        $newElement = null;
        $newElement['spec'] = $this->config['form']['elements'][$element['type']];

        if (isset($element['name'])) {
            $newElement['spec']['name'] = $newElement['spec']['attributes']['id'] = $element['name'];
        }

        if (isset($element['label'])) {
            $newElement['spec']['options']['label'] = $element['label'];
        }

        if (isset($element['placeholder'])) {
            $newElement['spec']['attributes']['placeholder'] = $element['placeholder'];
        }

        if (isset($element['type']) && in_array($element['type'], $this->elementsWithValueOptions) && isset($element['value_options'])) {

            if (is_array($element['value_options']))
            {
                // use array as options
                $newElement['spec']['options']['value_options'] = $element['value_options'];
            }
            if (is_string($element['value_options']))
            {
                // use string to look up in static-list-data
                $newElement['spec']['options']['value_options'] = $this->config['static-list-data'][$element['value_options']];
            }
        }

        return $newElement;
    }

    private function getFieldsets($fieldsets)
    {
        $thisFieldsets = array();
        foreach ($fieldsets as $fieldset) {
            $thisFieldset['name'] = $fieldset['name'];

            if (isset($fieldset['options'])) {
                $thisFieldset['options'] = $fieldset['options'];
            }

            $thisFieldset['elements'] = $this->getElements($fieldset['elements'], true);
            $thisFieldsets[]['spec'] = $thisFieldset;
        }
        return $thisFieldsets;
    }

    /**
     * Method to add a fieldset to an existing form config file.
     * @param array $formConfig
     * @param string $fieldset
     * @return array new config with fieldset merged in
     */
    public function addFieldset($formConfig, $fieldset)
    {
        $fieldsetConfig = $this->getFieldsetConfig($fieldset);

        if (isset($fieldsetConfig['options']['final_step']) && $fieldsetConfig['options']['final_step'])
        {
            // rename next button
            if (isset($formConfig['elements']['submit']))
            {
                $formConfig['elements']['submit']['label'] = 'Save & Next';
            }
        }
        if (isset($formConfig['fieldsets'])) {
            array_push($formConfig['fieldsets'], $fieldsetConfig);
        }
        else
        {
            $formConfig['fieldsets'][] = $fieldsetConfig;
        }

        return $formConfig;
    }

    /**
     * Returns the fieldset config for a given fieldset
     *
     * @param string $fieldset
     * @return array
     * @throws \Exception if fieldset not found.
     */
    protected function getFieldsetConfig($fieldset)
    {
        $path =  __DIR__ . "$this->fieldsetsPath$fieldset.fieldset.php";
        if (!file_exists($path)) {
            throw new \Exception("Fieldset $fieldset has no specification config!");
        }
        $fieldsetConfig = include $path;
        return $fieldsetConfig;
    }
}
