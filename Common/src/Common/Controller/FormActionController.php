<?php

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Controller;

use Common\Form\Elements\Types\Address;

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */
abstract class FormActionController extends AbstractActionController
{
    protected $enableCsrf = true;

    protected $validateForm = true;

    private $persist = true;

    private $fieldValues = array();

    /**
     * Allow csrf to be enabled and disabled
     */
    public function setEnabledCsrf($boolean = true)
    {
        $this->enableCsrf = $boolean;
    }

    /**
     * Switch form validation on or off
     *
     * @param boolean $validateForm
     */
    protected function setValidateForm($validateForm = true)
    {
        $this->validateForm = $validateForm;
    }

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    protected function getForm($type)
    {
        $form = $this->getServiceLocator()->get('OlcsCustomForm')->createForm($type);

        $form = $this->processPostcodeLookup($form);

        return $form;
    }

    /**
     * Process the postcode lookup functionality
     *
     * @param Form $form
     * @return Form
     */
    protected function processPostcodeLookup($form)
    {
        $request = $this->getRequest();

        $post = array();

        if ($request->isPost()) {

            $post = (array)$request->getPost();
        }

        $fieldsets = $form->getFieldsets();

        foreach ($fieldsets as $fieldset) {

            if ($fieldset instanceof Address) {

                $removeSelectFields = false;

                $name = $fieldset->getName();

                // If we haven't posted a form, or we haven't clicked find address
                if (isset($post[$name]['searchPostcode']['search'])
                    && !empty($post[$name]['searchPostcode']['search'])) {

                    $this->persist = false;

                    $postcode = trim($post[$name]['searchPostcode']['postcode']);

                    if (empty($postcode)) {

                        $removeSelectFields = true;

                        $fieldset->get('searchPostcode')->setMessages(
                            array('Please enter a postcode')
                        );
                    } else {

                        $addressList = $this->getAddressesForPostcode($postcode);

                        if (empty($addressList)) {

                            $removeSelectFields = true;

                            $fieldset->get('searchPostcode')->setMessages(
                                array('No addresses found for postcode')
                            );

                        } else {

                            $fieldset->get('searchPostcode')->get('addresses')->setValueOptions(
                                $this->getAddressService()->formatAddressesForSelect($addressList)
                            );
                        }
                    }
                } elseif (isset($post[$name]['searchPostcode']['select'])
                    && !empty($post[$name]['searchPostcode']['select'])) {

                    $this->persist = false;

                    $address = $this->getAddressForUprn($post[$name]['searchPostcode']['addresses']);

                    $removeSelectFields = true;

                    $addressDetails = $this->getAddressService()->formatPostalAddressFromBs7666($address);

                    $this->fieldValues[$name] = array_merge($post[$name], $addressDetails);

                } else {

                    $removeSelectFields = true;
                }

                if ($removeSelectFields) {
                    $fieldset->get('searchPostcode')->remove('addresses');
                    $fieldset->get('searchPostcode')->remove('select');
                }
            }
        }

        return $form;
    }

    protected function getAddressService()
    {
        return $this->getServiceLocator()->get('address');
    }

    protected function getAddressForUprn($uprn)
    {
        return $this->sendGet('postcode\address', array('id' => $uprn));
    }

    protected function getAddressesForPostcode($postcode)
    {
        return $this->sendGet('postcode\address', array('postcode' => $postcode));
    }

    protected function getFormGenerator()
    {
        return $this->getServiceLocator()->get('OlcsCustomForm');
    }

    protected function alterFormBeforeValidation($form)
    {
        return $form;
    }

    /**
     * Method to process posted form data and validate it and process a callback
     * @param type $form
     * @param type $callback
     * @return \Zend\Form
     */
    protected function formPost($form, $callback = null, $additionalParams = array())
    {
        if (!$this->enableCsrf) {
            $form->remove('csrf');
        }

        $form = $this->alterFormBeforeValidation($form);

        if ($this->getRequest()->isPost()) {

            $data = array_merge((array)$this->getRequest()->getPost(), $this->fieldValues);

            $form->setData($data);

            if (!$this->validateForm || ($this->persist && $form->isValid())) {

                if ($this->validateForm) {
                    $validatedData = $form->getData();
                } else {
                    $validatedData = $data;
                }

                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'params' => $additionalParams
                ];

                $params = array_merge($params, $this->getCallbackData());

                if (is_callable($callback)) {
                    $callback($params);
                } elseif (is_callable(array($this, $callback))) {
                    call_user_func_array(array($this, $callback), $params);
                } elseif (!empty($callback)) {
                    throw new \Exception('Invalid form callback: ' . $callback);
                }
            }
        }

        return $form;
    }

    /**
     * Adds data to the array passed to the formPost callback
     *
     * @return array
     */
    protected function getCallbackData()
    {
        return array();
    }

    /**
     * Generate a form with a callback
     *
     * @param string $name
     * @param callable $callback
     * @param boolean $tables
     * @return object
     */
    protected function generateForm($name, $callback, $tables = false)
    {
        $form = $this->getForm($name);

        if ($tables) {
            return $form;
        }

        return $this->formPost($form, $callback);
    }

    /**
     * Create a table form with data
     *
     * @param string $name
     * @param array $callbacks
     * @param mixed $data
     * @param array $tables
     * @return object
     */
    public function generateTableFormWithData($name, $callbacks, $data = null, $tables = array())
    {
        $callback = $callbacks['success'];

        $form = $this->generateFormWithData($name, $callbacks['success'], $data, true);

        foreach ($tables as $fieldsetName => $details) {

            $table = $this->getTable(
                $details['config'],
                $details['data'],
                (isset($details['variables']) ? $details['variables'] : array())
            );
            $form->get($fieldsetName)->get('table')->setTable($table);
            $form->get($fieldsetName)->get('rows')->setValue(count($table->getRows()));
        }

        $postData = null;

        if ($this->getRequest()->isPost()) {

            $postData = (array)$this->getRequest()->getPost();
        }

        foreach ($tables as $fieldsetName => $details) {

            if (
                !is_null($postData)
                && isset($postData[$fieldsetName]['action'])
                && !empty($postData[$fieldsetName]['action'])
            ) {

                $form = $this->disableEmptyValidation($form);
                $callback = $callbacks['crud_action'];
                break;
            }
        }

        return $this->formPost($form, $callback);
    }

    /**
     * Generate a form with data
     *
     * @param string $name
     * @param callable $callback
     * @param mixed $data
     * @param boolean $tables
     * @return object
     */
    public function generateFormWithData($name, $callback, $data = null, $tables = false)
    {
        $form = $this->generateForm($name, $callback, $tables);

        if (!$this->getRequest()->isPost() && is_array($data)) {
            $form->setData($data);
        }

        return $form;
    }

    /**
     * Disable empty validation
     *
     * @param object $form
     */
    private function disableEmptyValidation($form)
    {
        foreach ($form->getElements() as $key => $element) {

            if (empty($value)) {

                $form->getInputFilter()->get($key)->setAllowEmpty(true);
            }
        }

        foreach ($form->getFieldsets() as $key => $fieldset) {

            foreach ($fieldset->getElements() as $elementKey => $element) {

                $value = $element->getValue();

                if (empty($value)) {

                    $form->getInputFilter()->get($key)->get($elementKey)->setAllowEmpty(true);
                }
            }
        }

        return $form;
    }

    protected function processAdd($data, $entityName)
    {
        $data = $this->trimFormFields($data);

        return $this->makeRestCall($entityName, 'POST', $data);
    }

    protected function processEdit($data, $entityName)
    {
        $data = $this->trimFormFields($data);

        return $this->makeRestCall($entityName, 'PUT', $data);
    }

    protected function trimFormFields($data)
    {
        return $this->trimFields($data, array('csrf', 'submit', 'fields'));
    }

    protected function trimFields($data = array(), $unwantedFields = array())
    {
        foreach ($unwantedFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Find the address fields and process them accordingly
     *
     * @param array $data
     * @return array $data
     */
    protected function processAddressData($data, $addressName = 'address')
    {
        if (!isset($data['addresses'])) {
            $data['addresses'] = array();
        }

        unset($data[$addressName]['searchPostcode']);

        $data[$addressName]['country'] = str_replace('country.', '', $data[$addressName]['country']);

        $data['addresses'][$addressName] = $data[$addressName];

        unset($data[$addressName]);

        return $data;
    }

    /**
     * Check if a button was pressed
     *
     * @param string $button
     * @return bool
     */
    public function isButtonPressed($button)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            if (isset($data['form-actions'][$button])) {

                return true;
            }
        }

        return false;
    }
}
