<?php

/**
 * Creates a form from a form config file stored in the OlcsCommon module.config.php 'forms_path' or in
 * your local project module.config.php
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace Common\Service\Form;

use Zend\Form\Factory;

/**
 * Creates a form from a form config file stored in the OlcsCommon module.config.php 'forms_path' or in
 * your local project module.config.php
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
class OlcsCustomFormFactory extends Factory
{

    /**
     * Holds the application config
     *
     * @var array
     */
    private $config;

    /**
     * Holds the elements that have an array of options
     *
     * @var array
     */
    private $elementsWithValueOptions = array('select', 'selectDisabled', 'multicheckbox', 'radio', 'yesNoRadio');

    /**
     * Holds for form config
     *
     * @var array
     */
    public $baseFormConfig;

    /**
     * Holds the form paths
     *
     * @var array
     */
    public $formsPaths;

    /**
     * Holds the fieldset path
     *
     * @var string
     */
    public $fieldsetPath;

    /**
     * Pass in the config and setup the paths
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        if (isset($this->config['local_forms_path'])) {
            $this->formsPaths[] = $this->config['local_forms_path'];
        }
        $this->formsPaths[] = $this->config['forms_path'];
        $this->fieldsetsPath = $this->config['fieldsets_path'];
        parent::__construct();
    }

    /**
     * Create a form
     *
     * @param string $type
     */
    public function createForm($type)
    {
        if (empty($this->baseFormConfig)) {
            $this->baseFormConfig = $this->getFormConfig($type);
        }
        if (!isset($this->baseFormConfig[$type])) {
            throw new \Exception("Form $type has no specification config");
        }
        $formConfig = $this->createFormConfig($this->baseFormConfig[$type]);
        return parent::createForm($formConfig);
    }

    /**
     * Get the form config for the type
     *
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public function getFormConfig($type)
    {
        foreach ($this->formsPaths as $formsPath) {
            $path = $formsPath . $type . '.form.php';
            if (file_exists($path)) {
                $formConfig = include $path;
                return $formConfig;
            }
        }
        throw new \Exception("Form $type config file cannot be found");
    }

    /**
     * Set the form config
     *
     * @param array $config
     */
    public function setFormConfig(array $config)
    {
        $this->baseFormConfig = $config;
    }

    /**
     * Create the form config
     *
     * @param array $formConfig
     * @return array
     */
    private function createFormConfig($formConfig)
    {
        if (isset($formConfig['fieldsets'])) {
            $formConfig['fieldsets'] = $this->getFieldsets($formConfig['fieldsets']);
        }

        if (isset($formConfig['elements'])) {
            $formConfig['elements']['crsf'] = array('type' => 'crsf');
            $formConfig['elements'] = $this->getElements($formConfig['elements']);
        }

        return $formConfig;
    }

    /**
     * Get elements
     *
     * @param array $elements
     * @return array
     */
    private function getElements($elements)
    {
        $thisElements = [];
        foreach ($elements as $key => $element) {

            if (isset($element['elements'])) {

                $subElements = $this->getElements($element['elements']);

                foreach ($subElements as $subKey => $subElement) {

                    $subElement['name'] = $key . '_' . (isset($subElement['name']) ? $subElement['name'] : $subKey);

                    $thisElements[] = $this->getElement($subElement);
                }
            } else {

                $element['name'] = isset($element['name']) ? $element['name'] : $key;

                $thisElements[] = $this->getElement($element);
            }
        }
        return $thisElements;
    }

    /**
     * Get element
     *
     * @param array $element
     * @return array
     */
    private function getElement($element)
    {
        $newElement = null;

        $newElement['spec'] = $this->config['form']['elements'][$element['type']];

        // Sets the type to a filter class for filtering and validation
        if (isset($element['filters'])) {
            $newElement['spec']['type'] = $element['filters'];
        }

        if (isset($element['name'])) {
            $newElement['spec']['name'] = $newElement['spec']['attributes']['id'] = $element['name'];
        }

        if (isset($element['class'])) {
            $newElement['spec']['attributes']['class'] = $element['class'];
        }

        if (isset($element['validators'])) {
            $newElement['spec']['options']['validators'] = $element['validators'];
        }

        if (isset($element['additional_validators'])) {
            if (!is_array($newElement['spec']['validators'])) {
                $newElement['spec']['options']['validators'] = array();
            }
            $newElement['spec']['options']['validators'][] = $element['additional_validators'];
        }

        if (isset($element['label'])) {
            $newElement['spec']['options']['label'] = $element['label'];
        }

        if (isset($element['placeholder'])) {
            $newElement['spec']['attributes']['placeholder'] = $element['placeholder'];
        }

        if (isset($element['type'])
            && in_array($element['type'], $this->elementsWithValueOptions)
            && isset($element['value_options'])) {

            if (is_array($element['value_options'])) {
                // use array as options
                $newElement['spec']['options']['value_options'] = $element['value_options'];
            }
            if (is_string($element['value_options'])) {
                // use string to look up in static-list-data
                $newElement['spec']['options']['value_options'] = $this->getListValues($element['value_options']);
            }
        }

        if (isset($newElement['spec']['options']['value_options'])
            && is_string($newElement['spec']['options']['value_options'])) {

            $newElement['spec']['options']['value_options'] = $this->getListValues(
                $newElement['spec']['options']['value_options']
            );
        }

        // input for hidden values
        if (isset($element['attributes']['value'])) {

            $newElement['spec']['attributes']['value'] = $element['attributes']['value'];
        }

        return $newElement;
    }

    /**
     * Return the list values
     *
     * @param string $name
     * @return array
     */
    private function getListValues($name)
    {
        return isset($this->config['static-list-data'][$name]) ? $this->config['static-list-data'][$name] : array();
    }

    /**
     * Get fieldsets
     *
     * @param array $fieldsets
     * @return array
     */
    private function getFieldsets($fieldsets)
    {
        $thisFieldsets = array();
        foreach ($fieldsets as $fieldset) {

            // This logic pulls in a fieldset from config
            if (isset($fieldset['type'])) {

                $newFieldset = array();

                $newFieldset = $this->getFieldsetConfig($fieldset['type']);

                unset($fieldset['type']);

                $newFieldset = array_merge($newFieldset, $fieldset);

                $fieldset = $newFieldset;
            }

            $thisFieldset['name'] = $fieldset['name'];

            if (isset($fieldset['options'])) {
                $thisFieldset['options'] = $fieldset['options'];
            }

            $thisFieldset['elements'] = $this->getElements($fieldset['elements']);
            $thisFieldsets[]['spec'] = $thisFieldset;
        }
        return $thisFieldsets;
    }

    /**
     * Method to add a fieldset to an existing form config file.
     *
     * @param array $formConfig
     * @param string $fieldset
     * @return array new config with fieldset merged in
     */
    public function addFieldset($formConfig, $fieldset)
    {
        $fieldsetConfig = $this->getFieldsetConfig($fieldset);

        if (isset($fieldsetConfig['options']['final_step']) && $fieldsetConfig['options']['final_step']) {
            // rename next button
            if (isset($formConfig['elements']['submit'])) {
                $formConfig['elements']['submit']['label'] = 'Save & Next';
            }
        }
        if (isset($formConfig['fieldsets'])) {
            array_push($formConfig['fieldsets'], $fieldsetConfig);
        } else {
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
        $path = $this->fieldsetsPath . $fieldset . '.fieldset.php';

        if (!file_exists($path)) {
            throw new \Exception("Fieldset $fieldset has no specification config!");
        }
        $fieldsetConfig = include $path;
        return $fieldsetConfig;
    }
}
