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
    private $persist = true;

    private $fieldValues = array();

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

    /**
     * Method to process posted form data and validate it and process a callback
     * @param type $form
     * @param type $callback
     * @return \Zend\Form
     */
    protected function formPost($form, $callback = null, $additionalParams = array())
    {
        if ($this->getRequest()->isPost()) {

            $data = array_merge((array)$this->getRequest()->getPost(), $this->fieldValues);

            $form->setData($data);

            if ($this->persist && $form->isValid()) {

                $validatedData = $form->getData();

                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'params' => $additionalParams
                ];

                $params = array_merge($params, $this->getCallbackData());
                if (!empty($callback)) {
                    if (is_callable($callback)) {
                        $callback($params);
                    }
                    call_user_func_array(array($this, $callback), $params);
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
     * @param boolean $edit
     * @return object
     */
    public function generateTableFormWithData($name, $callbacks, $data = null, $tables = array(), $edit = false)
    {
        $callback = $callbacks['success'];

        $form = $this->generateFormWithData($name, $callbacks['success'], $data, $edit, true);

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
     * @param boolean $edit
     * @param boolean $tables
     * @return object
     */
    public function generateFormWithData($name, $callback, $data = null, $edit = false, $tables = false)
    {
        $form = $this->generateForm($name, $callback, $tables);

        if ($edit && $this->getRequest()->isPost()) {

            $form->setData($this->getRequest()->getPost());

        } elseif (is_array($data)) {

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
        return $this->trimFields($data, array('crsf', 'submit', 'fields'));
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

        $data[$addressName]['country'] = str_replace('country.', '', $data[$addressName]['country']);

        $data['addresses'][$addressName] = $data[$addressName];

        unset($data[$addressName]);

        return $data;
    }
}
