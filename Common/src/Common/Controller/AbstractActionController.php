<?php

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @package     olcscommon
 * @subpackage  controller
 * @author      Pelle Wessman <pelle.wessman@valtech.se>
 */

namespace Common\Controller;

abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController
{

    use \Common\Util\ResolveApiTrait;
    use \Common\Util\LoggerTrait;
    use \Common\Util\FlashMessengerTrait;
    use \Common\Util\RestCallTrait;

    /**
     * Set navigation for breadcrumb
     * @param type $label
     * @param type $params
     */
    protected function setBreadcrumb($route, $params)
    {
        $navigation = $this->getServiceLocator()->get('navigation');
        $page = $navigation->findBy('route', $route);
        $page->setParams($params);
    }

    /**
     * Get all request params from the query string and route and send back the required ones
     * @param type $keys
     * @return type
     */
    protected function getParams($keys)
    {
        $params = [];
        $getParams = array_merge($this->getEvent()->getRouteMatch()->getParams(), $this->getRequest()->getQuery()->toArray());
        foreach ($getParams as $key => $value) {
            if (in_array($key, $keys)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

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
    protected function formPost($form, $callback, $additionalParams = array())
    {
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $validatedData = $form->getData();
                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'journeyData' => $this->getJourneyData(),
                    'params' => $additionalParams
                ];
                if (is_callable($callback)) {
                    $callback($params);
                }

                call_user_func_array(array($this, $callback), $params);
            }
        }
        return $form;
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
    protected function generateFormWithData($name, $callback, $data = null)
    {
        $form = $this->generateForm($name, $callback);

        if (is_array($data)) {
            $form->setData($data);
        }

        return $form;
    }

    /**
     * Generate form from GET call
     *
     * @todo Need to do something with $return to format the data
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

    /**
     * Method to gather any info relevent to the journey. This is passed
     * to the processForm method and any call back used.
     *
     * @return array
     */
    private function getJourneyData()
    {
        return [
            'section' => $this->getCurrentSection(),
            'step' => $this->getCurrentStep()
        ];
    }

}
