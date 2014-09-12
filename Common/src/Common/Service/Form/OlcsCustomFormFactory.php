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

    private $dynamicOptions;

    /**
     * Holds the elements that have an array of options
     *
     * @var array
     */
    private $elementsWithValueOptions = array(
        'select',
        'select-noempty',
        'selectDisabled',
        'multicheckbox',
        'confirm-checkbox',
        'radio',
        'yesNoRadio'
    );

    /**
     * Holds the form config
     *
     * @var array
     */
    public $baseFormConfig;

    /**
     * Holds the form type
     *
     * @var string
     */
    protected $type;

    /**
     * Holds the form paths
     *
     * @var array
     */
    public $formsPaths = [];

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
            $this->formsPaths = array_merge($this->formsPaths, (array) $this->config['local_forms_path']);
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
        $this->type = $type;

        if (!isset($this->baseFormConfig[$type])) {
            $this->baseFormConfig = $this->getFormConfig($type);
        }

        if (!isset($this->baseFormConfig[$type])) {
            throw new \Exception("Form $type has no specification config");
        }

        $formConfig = $this->injectDynamicOptions(
            $this->createFormConfig($this->baseFormConfig[$type]),
            $this->getDynamicOptions()
        );

        $form = parent::createForm($formConfig);

        return $this->fixId($form);
    }

    /**
     * Sets the id of the form to _form.
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function fixId(\Zend\Form\Form $form)
    {
        $id = $form->getAttribute('id');

        if ('' != $id) {
            $form->setAttribute('id', $id . '_form');
        } else {
            $form->setAttribute('id', $form->getAttribute('name') . '_form');
        }

        return $form;
    }

    /**
     * Get the form config for the type
     *
     * @param string $type
     *
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

    /*
     * Set dynamic options
     *
     * @param $dynamicOptions Optional;
     */
    public function setDynamicOptions($dynamicOptions = null)
    {
        $this->dynamicOptions = $dynamicOptions;
    }

    /**
     * Get dynamic options
     *
     * @return miexed
     */
    public function getDynamicOptions()
    {
        return $this->dynamicOptions;
    }

    /**
     * Iterate through config and inject dynamic options
     *
     * @param $config
     * @return mixed
     */
    private function injectDynamicOptions($config, $dynamicOptions)
    {
        if (empty($dynamicOptions)) {
            return $config;
        }

        foreach ($config as &$value) {
            if (is_array($value)) {

                $value = $this->injectDynamicOptions($value, $dynamicOptions);

            } else {

                foreach ($dynamicOptions as $dKey => $dVal) {

                    if ($value == '{{' . $dKey . '}}') {
                        $value = $dVal;
                    }
                }
            }
        }
        return $config;
    }

    /**
     * Set the form config
     *
     * @param array $config
     */
    public function setFormConfig(array $config)
    {
        $this->baseFormConfig = $config;
        return $this;
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
            $formConfig = $this->maybeAddHiddenSubmitButton($formConfig);
        }

        $formConfig['elements']['csrf'] = array('type' => 'csrf');

        $formConfig['elements'] = $this->getElements($formConfig['elements']);

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

        $config = $this->baseFormConfig[$this->type];
        $forceDisabled = isset($config['disabled']) && $config['disabled'];

        // Sets the type to a filter class for filtering and validation
        if (isset($element['filters'])) {
            $newElement['spec']['type'] = $element['filters'];
        }

        if (isset($element['name'])) {
            $newElement['spec']['name'] = $newElement['spec']['attributes']['id'] = $element['name'];
        }

        $mergeAttributes = array('class', 'placeholder', 'data-container-class', 'disabled');

        foreach ($mergeAttributes as $attribute) {
            if (isset($element[$attribute])) {
                $newElement['spec']['attributes'][$attribute] = $element[$attribute];
            }
        }

        if ($forceDisabled && (!isset($element['enable']) || !$element['enable'])) {
            $newElement['spec']['attributes']['disabled'] = 'disabled';
        }

        $mergeOptions = array('label', 'label_attributes', 'description', 'hint', 'route', 'value-label');

        foreach ($mergeOptions as $option) {
            if (isset($element[$option])) {
                $newElement['spec']['options'][$option] = $element[$option];
            }
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

            $thisFieldset = array();

            // This logic pulls in a fieldset from config
            if (isset($fieldset['type'])) {

                $newFieldset = $this->getFieldsetConfig($fieldset['type']);

                unset($fieldset['type']);

                $newFieldset = array_merge($newFieldset, $fieldset);

                $fieldset = $newFieldset;
            }

            $thisFieldset['name'] = $fieldset['name'];

            if (isset($fieldset['options'])) {
                $thisFieldset['options'] = $fieldset['options'];
            }

            if (isset($fieldset['attributes'])) {
                $thisFieldset['attributes'] = $fieldset['attributes'];
            }

            $thisFieldset['elements'] = $this->getElements($fieldset['elements']);

            if (isset($fieldset['type']) && class_exists($fieldset['type'])) {

                $thisFieldset['type'] = $fieldset['type'];
            }

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

    /**
     * Adds hidden submit button to handle Enter key
     *
     * @param array $formConfig
     * @return array
     */
    protected function maybeAddHiddenSubmitButton($formConfig)
    {
        $needToBreak = false;
        foreach ($formConfig['fieldsets'] as $fieldset) {
            if (isset($fieldset['spec']['name']) && $fieldset['spec']['name'] == 'form-actions' &&
                isset($fieldset['spec']['elements']) && is_array($fieldset['spec']['elements'])) {
                foreach ($fieldset['spec']['elements'] as $element) {
                    if (isset($element['spec']['type']) &&
                        $element['spec']['type'] == '\Common\Form\Elements\InputFilters\ActionButton' &&
                        $element['spec']['name'] == 'submit') {
                        $formConfig['elements']['hiddenSubmit'] =
                            array(
                                'type' => 'submit',
                                'name' => 'form-actions[submit]',
                                'label' => isset($element['spec']['options']['label']) ?
                                    $element['spec']['options']['label'] : 'Save',
                                'class' => 'visually-hidden'
                            );
                        $needToBreak = true;
                        break;
                    }
                }
            }
            if ($needToBreak) {
                break;
            }
        }
        return $formConfig;
    }
}
