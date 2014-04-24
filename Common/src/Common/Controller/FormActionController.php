<?php

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Controller;

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */
abstract class FormActionController extends AbstractActionController
{

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    protected function getForm($type)
    {
        $form = $this->getServiceLocator()->get('OlcsCustomForm')->createForm($type);
        return $form;
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
    protected function formPost($form, $callback=null, $additionalParams = array())
    {

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
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
     * @return object
     */
    protected function generateForm($name, $callback)
    {
        $form = $this->getForm($name);

        return $this->formPost($form, $callback);
    }

    /**
     * Generate a form with data
     *
     * @param string $name
     * @param callable $callback
     * @param mixed $data
     * @return object
     */
    public function generateFormWithData($name, $callback, $data = null, $edit = false)
    {
        $form = $this->generateForm($name, $callback);

        if ($edit && $this->getRequest()->isPost()) {

            $form->setData($this->getRequest()->getPost());

        } elseif (is_array($data)) {

            $form->setData($data);
        }

        return $form;
    }

    /**
     * Generate form from GET call
     *
     * @todo Maybe need to do something with $return to format the data
     *
     * @param string $name
     * @param callable $callback
     * @param string $service
     * @param int $id
     *
     * @return object
     */
    protected function generateFormFromGet($name, $callback, $service, $id)
    {
        $return = $this->makeRestCall($service, 'GET', array('id' => $id));

        return $this->generateFormWithData($name, $callback, $return);
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
