<?php

/**
 * Generic Methods from legacy Abstract Action controller
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
namespace Common\Controller\Traits;

use Zend\View\Model\ViewModel;

/**
 * Generic Methods from legacy Abstract Action controller
 *
 * @author Alex Peshkov <alex.pehkov@valtech.co.uk>
 */
trait GenericMethods
{
    /*
     * Load an array of script files which will be rendered inline inside a view
     *
     * @param array $scripts
     * @return array
     */
    protected function loadScripts($scripts)
    {
        return $this->getServiceLocator()->get('Script')->loadFiles($scripts);
    }

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    public function getForm($type)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm($type);
        $formHelper->setFormActionFromRequest($form, $this->getRequest());
        $formHelper->processAddressLookupForm($form, $this->getRequest());

        return $form;
    }

    /**
     * Method to process posted form data and validate it and process a callback
     * @param type $form
     * @param type $callback
     * @param array $additionalParams
     * @param bool $validateForm
     * @param bool $enableCsrf
     * @return \Zend\Form\Form
     */
    public function formPost($form, $callback = null, $additionalParams = [], $validateForm = true, $enableCsrf = true)
    {
        if (!$enableCsrf) {
            $form->remove('csrf');
        }

        if (method_exists($this, 'alterFormBeforeValidation')) {
            $form = $this->alterFormBeforeValidation($form);
        }

        if ($this->getRequest()->isPost()) {

            $data = (array)$this->getRequest()->getPost();
            $form->setData($data);

            if (method_exists($this, 'postSetFormData')) {
                $form = $this->postSetFormData($form);
            }

            /**
             * validateForm is true by default, we set it to false if we want to continue processing the form without
             * validation.
             */
            if (!$validateForm || $form->isValid()) {

                if ($validateForm) {
                    $validatedData = $form->getData();
                } else {
                    $validatedData = $data;
                }

                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'params' => $additionalParams
                ];

                $this->callCallbackIfExists($callback, $params);
            } elseif (!$validateForm || !$form->isValid()) {
                if (method_exists($this, 'onInvalidPost')) {
                    $this->onInvalidPost($form);
                }
            }
        }

        return $form;
    }

    /**
     * Calls the callback function/method if exists.
     *
     * @param unknown_type $callback
     * @param unknown_type $params
     * @throws \Exception
     */
    public function callCallbackIfExists($callback, $params)
    {
        if (is_callable($callback)) {
            $callback($params);
        } elseif (is_callable(array($this, $callback))) {
            call_user_func_array(array($this, $callback), $params);
        } elseif (!empty($callback)) {
            throw new \Exception('Invalid form callback: ' . $callback);
        }
    }

    /**
     * Wraps the redirect()->toRoute to help with unit testing
     *
     * @param string $route
     * @param array $params
     * @param array $options
     * @param bool $reuse
     * @return \Zend\Http\Response
     */
    public function redirectToRoute($route = null, $params = array(), $options = array(), $reuse = false)
    {
        return $this->redirect()->toRoute($route, $params, $options, $reuse);
    }

    /**
     * Wraps the redirect()->toRouteAjax method to help with unit testing
     *
     * @param string $route
     * @param array $params
     * @param array $options
     * @param bool $reuse
     * @return \Zend\Http\Response
     */
    public function redirectToRouteAjax($route = null, $params = array(), $options = array(), $reuse = false)
    {
        return $this->redirect()->toRouteAjax($route, $params, $options, $reuse);
    }

    /**
     * Build a table from config and results, and return the table object
     *
     * @param string $table
     * @param array $results
     * @param array $data
     * @return string
     */
    public function getTable($table, $results, $data = array())
    {
        if (!isset($data['url'])) {
            $data['url'] = $this->getPluginManager()->get('url');
        }

        return $this->getServiceLocator()->get('Table')->buildTable($table, $results, $data, false);
    }

    /**
     * Check if a button was pressed
     *
     * @param string $button
     * @param array $data
     * @return bool
     */
    public function isButtonPressed($button, $data = null)
    {
        $request = $this->getRequest();

        if (is_null($data)) {
            $data = (array)$request->getPost();
        }

        return $request->isPost() && isset($data['form-actions'][$button]);
    }

    /**
     * Get param from route
     *
     * @param string $name
     * @return string
     */
    public function getFromRoute($name)
    {
        return $this->params()->fromRoute($name);
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
    public function generateFormWithData($name, $callback, $data = null, $tables = false, $enableCsrf = true)
    {
        $form = $this->generateForm($name, $callback, $tables, $enableCsrf);

        if (!$this->getRequest()->isPost() && is_array($data)) {
            $form->setData($data);
        }

        return $form;
    }

    /**
     * Generate a form with a callback
     *
     * @param string $name
     * @param callable $callback
     * @param boolean $tables
     * @return object
     */
    protected function generateForm($name, $callback, $tables = false, $enableCsrf = true)
    {
        $form = $this->getForm($name);

        if ($tables) {
            return $form;
        }

        return $this->formPost($form, $callback, [], true, $enableCsrf);
    }
}