<?php

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooperr <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Controller;

use Common\Util;
use Zend\Mvc\MvcEvent;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Http\Response;
use Zend\InputFilter\Input;
use Zend\View\Model\ViewModel;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;
use Zend\Filter\Word\DashToCamelCase;

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @todo Need to move as much business logic as possible into services
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooperr <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController
{
    use Util\LoggerTrait,
        Util\FlashMessengerTrait,
        Util\RestCallTrait,
        Traits\ViewHelperManagerAware;

    /**
     * Holds the identifier
     *
     * @var int
     */
    protected $identifier;

    /**
     * Identifier name
     *
     * @var string
     */
    protected $identifierName = 'id';

    /**
     * Holds the isAction
     *
     * @var boolean
     */
    protected $isAction;

    /**
     * Holds the action name
     *
     * @var string
     */
    private $actionName;

    /**
     * Holds the action id
     *
     * @var int
     */
    protected $actionId;

    /**
     * Holds the caught response
     *
     * @var mixed
     */
    protected $caughtResponse = null;

    /**
     * Holds the layout
     *
     * @var string
     */
    protected $layout;

    /**
     * Data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    /**
     * Holds the Data Bundle
     *
     * @var array
     */
    protected $dataBundle = null;

    /**
     * Holds the service name
     *
     * @var string
     */
    protected $service = null;

    /**
     * Holds the form tables
     *
     * @var array
     */
    protected $formTables;

    /**
     * Cache the section services
     *
     * @var array
     */
    private $sectionServices = array();

    /**
     * Holds the section service name
     *
     * @var string
     */
    protected $sectionServiceName = 'Generic';

    /**
     * onDispatch now populates this with the route for the index of
     * the controller curently being executed.
     *
     * @var array
     */
    protected $indexRoute = [];

    /**
     * The current page's main title
     *
     * @var string
     */
    protected $pageTitle = null;

    /**
     * The current page's sub title, if applicable
     *
     * @var string
     */
    protected $pageSubTitle = null;

    /**
     * The current page's extra layout, over and above the
     * standard base template
     *
     * @var string
     */
    protected $pageLayout = null;

    /**
     * Holds any inline scripts for the current page
     *
     * @var array
     */
    protected $inlineScripts = [];

    protected $enableCsrf = true;
    protected $validateForm = true;

    private $fieldValues = array();
    private $loggedInUser;

    /**
     * @codeCoverageIgnore
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->setupIndexRoute($e);

        $this->preOnDispatch();
        parent::onDispatch($e);
    }

    /**
     * Sets up the index route array.
     *
     * @codeCoverageIgnore
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function setupIndexRoute(MvcEvent $e)
    {
        if (empty($this->indexRoute)) {
            $this->indexRoute = [
                $e->getRouteMatch()->getMatchedRouteName(),
                array_merge($this->params()->fromRoute(), ['action' => 'index', 'id' => null])
            ];
        }
    }

    /**
     * Olcs specific onDispatch method.
     */
    public function preOnDispatch()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();

        $headers->addHeaderLine('Cache-Control', 'no-cache, must-revalidate');
        $headers->addHeaderLine('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        $this->setLoggedInUser(1);
    }

    /**
     * Does what it says on the tin.
     *
     * @codeCoverageIgnore
     * @return mixed
     */
    public function redirectToIndex()
    {
        return call_user_func_array([$this->redirect(), 'toRoute'], $this->indexRoute);
    }

    /**
     * Set navigation for breadcrumb
     * @param type $label
     * @param type $params
     */
    public function setBreadcrumb($navRoutes = array())
    {
        foreach ($navRoutes as $route => $routeParams) {
            $navigation = $this->getServiceLocator()->get('navigation');
            $page = $navigation->findBy('route', $route);
            if ($page) {
                $page->setParams($routeParams);
            }
        }
    }

    /**
     * Get all request params from the query string and route and send back the required ones
     * @param type $keys
     * @return type
     */
    public function getParams($keys)
    {
        $params = [];
        $getParams = $this->getAllParams();
        foreach ($getParams as $key => $value) {
            if (in_array($key, $keys)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    public function getAllParams()
    {
        $getParams = array_merge(
            $this->getEvent()->getRouteMatch()->getParams(),
            $this->getRequest()->getQuery()->toArray()
        );

        return $getParams;
    }

    /**
     * Check for crud actions
     *
     * @param string $route
     * @param array $params
     * @param string $itemIdParam
     *
     * @return boolean
     */
    protected function checkForCrudAction($route = null, $params = array(), $itemIdParam = 'id')
    {
        $action = $this->getCrudActionFromPost();

        if (empty($action)) {
            return false;
        }

        $action = strtolower($action);

        $response = $this->checkForAlternativeCrudAction($action);

        if ($response instanceof Response) {
            return $response;
        }

        $params = array_merge($params, array('action' => $action));

        $options = array();

        $action = $this->getActionFromFullActionName($action);

        if ($action !== 'add') {

            $id = $this->params()->fromPost('id');

            if (empty($id)) {

                $this->crudActionMissingId();
                return false;
            }

            if (is_array($id) && count($id) === 1) {
                $id = $id[0];
            }

            // If we have an array of id's we need to use a query string param rather than the route
            if (is_array($id)) {
                $options = array(
                    'query' => array(
                        $itemIdParam => $id
                    )
                );
            } else {
                $params[$itemIdParam] = $id;
            }
        }

        return $this->redirect()->toRoute($route, $params, $options, true);
    }

    /**
     * Get the last part of the action from the action name
     *
     * @return string
     */
    protected function getActionFromFullActionName($action = null)
    {
        if ($action == null) {
            return '';
        }

        if (!strstr($action, '-')) {
            return $action;
        }

        $parts = explode('-', $action);
        return array_pop($parts);
    }

    /**
     * Do nothing, this method can be overridden to hijack the crud action check
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {

    }

    /**
     * We can now extend our check for crud action
     *
     * @return string
     */
    protected function getCrudActionFromPost()
    {
        return $this->params()->fromPost('action');
    }

    /**
     * Called when a crud action is missing a required ID
     */
    protected function crudActionMissingId()
    {
        $this->addWarningMessage('Please select a row');
        return $this->redirectToRoute(null, array(), array(), true);
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
     * Return a new view model
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function getViewModel($params = array())
    {
        return new ViewModel($params);
    }

    /**
     * Get url from route
     *
     * @param string $route
     * @return string
     */
    public function getUrlFromRoute($route, $params = array())
    {
        return $this->url()->fromRoute($route, $params);
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
     * Get param from post
     *
     * @param string $name
     * @return string
     */
    public function getFromPost($name)
    {
        return $this->params()->fromPost($name);
    }

    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }

    public function setLoggedInUser($id)
    {
        $this->loggedInUser = $id;
        return $this;
    }

    /**
     * Get uploader
     *
     * @return object
     */
    public function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader')->getUploader();
    }

    /**
     * Wrapper method to render a view with optional title and sub title values
     *
     * @param string|ViewModel $view
     * @param string $pageTitle
     * @param string $pageSubTitle
     *
     * @return ViewModel
     */
    protected function renderView($view, $pageTitle = null, $pageSubTitle = null)
    {
        // allow for very simple views to be passed as a string. Obviously this
        // precludes the passing of any template variables but can still come
        // in handy when no extra variables need to be set
        if (is_string($view)) {
            $viewName = $view;
            $view = new ViewModel();
            $view->setTemplate($viewName);
        }

        // no, I don't know why it's not getTerminal or isTerminal either...
        if ($view->terminate()) {
            return $view;
        }

        // allow both the page title and sub title to be passed as explicit
        // arguments to this method
        if ($pageTitle !== null) {
            $this->pageTitle = $pageTitle;
        }

        if ($pageSubTitle !== null) {
            $this->pageSubTitle = $pageSubTitle;
        }

        $viewVariables = array_merge(
            (array)$view->getVariables(),
            [
                'pageTitle' => $this->pageTitle,
                'pageSubTitle' => $this->pageSubTitle
            ]
        );

        // every page has a header, so no conditional logic needed here
        $header = new ViewModel($viewVariables);
        $header->setTemplate('layout/partials/header');

        // allow a controller to specify a more specific page layout to use
        // in addition to the base one all views inherit from
        if ($this->pageLayout !== null) {
            $layout = $this->pageLayout;
            if (is_string($layout)) {
                $viewName = $layout;
                $layout = new ViewModel();
                $layout->setTemplate('layout/' . $viewName);
                $layout->setVariables($viewVariables);
            }

            $layout->addChild($view, 'content');

            // reassign the main view to be this new layout so that when we
            // come to create the base view it can just add '$view' without
            // having to care what it is
            $view = $layout;
        }

        // we always inherit from the same base layout, unless the request
        // was asynchronous in which case we render a much simpler wrapper,
        // but one which will include any inline JS we need
        // note that if templates don't want this behaviour they can either
        // mark themselves as terminal, or simply not opt-in to this helper
        $template = $this->getRequest()->isXmlHttpRequest() ? 'ajax' : 'base';
        $base = new ViewModel();
        $base->setTemplate('layout/' . $template)
            ->setTerminal(true)
            ->setVariables($viewVariables)
            ->addChild($header, 'header')
            ->addChild($view, 'content');

        return $base;
    }

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
     * Get the inline scripts
     *
     * @return array
     */
    protected function getInlineScripts()
    {
        return $this->inlineScripts;
    }

    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        if ($this instanceof CrudInterface) {
            $this->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, array($this, 'cancelButtonListener'), 100);
        }
    }

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
     * Switch form persistence on or off
     *
     * @param boolean $persist
     */
    protected function setPersist($persist = true)
    {
        $this->getSectionService()->setPersist($persist);
    }

    /**
     * Get persist
     *
     * @return boolean
     */
    protected function getPersist()
    {
        return $this->getSectionService()->getPersist();
    }

    /**
     * set the field value for a given key. This allows us
     * to override form data which has been previously set
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setFieldValue($key, $value)
    {
        $this->fieldValues[$key] = $value;
    }

    protected function normaliseFormName($name, $ucFirst = false)
    {
        $name = str_replace([' ', '_'], '-', $name);

        $filter = new DashToCamelCase();

        if (!$ucFirst) {
            return lcfirst($filter->filter($name));
        }

        return $filter->filter($name);
    }

    /**
     * @param $type
     * @return \Zend\Form\Form
     * @TO-DO Turn this into a proper service/factory for forms
     */
    protected function getFormClass($type)
    {
        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');

        foreach (['Olcs', 'SelfServe', 'Common'] as $namespace) {
            $class = $namespace . '\\Form\\Model\\Form\\' . $this->normaliseFormName($type, true);
            if (class_exists($class)) {
                return $annotationBuilder->createForm($class);
            }
        }
        return $this->getServiceLocator()->get('OlcsCustomForm')->createForm($type);
    }

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    protected function getForm($type)
    {
        $form = $this->getFormClass($type);

        // The vast majority of forms thus far don't have actions, but
        // that means when rendered out of context (e.g. in a JS modal) they
        // submit the parent page.
        // Adding an explicit attribute should be completely backwards compatible
        // because browsers interpret no action as submit the current page
        if (!$form->hasAttribute('action')) {
            $form->setAttribute('action', $this->getRequest()->getUri()->getPath());
        }

        $form = $this->getSectionService()->processPostcodeLookup($form);

        return $form;
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
    public function formPost($form, $callback = null, $additionalParams = array())
    {
        if (!$this->enableCsrf) {
            $form->remove('csrf');
        }

        $form = $this->alterFormBeforeValidation($form);

        if ($this->getRequest()->isPost()) {

            $data = array_merge((array)$this->getRequest()->getPost(), $this->fieldValues);

            $form->setData($data);

            $form = $this->postSetFormData($form);

            /**
             * validateForm is true by default, we set it to false if we want to continue processing the form without
             * validation.
             */
            if (!$this->validateForm || ($this->getPersist() && $form->isValid())) {

                if ($this->validateForm) {
                    $validatedData = $form->getData();
                } else {
                    $validatedData = $data;
                }

                $params = array_merge(
                    [
                        'validData' => $validatedData,
                        'form' => $form,
                        'params' => $additionalParams
                    ],
                    $this->getCallbackData()
                );

                $this->callCallbackIfExists($callback, $params);
            }
        }

        return $form;
    }

    /**
     * Added extra method called after setting form data
     *
     * @param Form $form
     * @return Form
     */
    protected function postSetFormData($form)
    {
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

            $form->get($fieldsetName)->get('table')->setTable($table, $fieldsetName);
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
                $form->getInputFilter()->get($key)->setValidatorChain(
                    new ValidatorChain()
                );
            }
        }

        foreach ($form->getFieldsets() as $key => $fieldset) {

            foreach ($fieldset->getElements() as $elementKey => $element) {

                $value = $element->getValue();

                if (empty($value)) {

                    $form->getInputFilter()->get($key)->get($elementKey)->setAllowEmpty(true);
                    $form->getInputFilter()->get($key)->get($elementKey)->setValidatorChain(
                        new ValidatorChain()
                    );
                }
            }
        }

        return $form;
    }

    public function processAdd($data, $entityName)
    {
        $data = $this->trimFormFields($data);

        $result = $this->makeRestCall($entityName, 'POST', $data);

        $data['id'] = $result['id'];
        $this->generateDocument($data);

        return $result;
    }

    public function processEdit($data, $entityName)
    {
        $data = $this->trimFormFields($data);

        $result = $this->makeRestCall($entityName, 'PUT', $data);

        $this->generateDocument($data);

        return $result;

    }

    /**
     * Method to trigger generation of a document providing a generate checkbox
     * is found in $data
     *
     * @param array $data
     * @return array
     * @throws \RuntimeException
     */
    protected function generateDocument($data = array())
    {
        $documentData = [];
        if (isset($data['document']['generate']) && $data['document']['generate'] == '1') {

            if (!method_exists($this, 'mapDocumentData')) {
                throw new \RuntimeException('Controller requires mapDocumentData method');
            }
            $bookmarks = $this->mapDocumentData($data);

            $documentData = $this->sendPost(
                'Olcs\Document\GenerateRtf', [
                    'data' => [
                        'formName' => $data['document']['formName'],
                        'licence' => $this->fromRoute('licence'),
                        'case' => $this->fromRoute('case'),
                        'id' => $data['id']
                    ],
                    'bookmarks' => $bookmarks,
                    'country' =>
                        isset($data['document']['country']) ?
                        $data['document']['country'] : 'en_GB',
                    'templateId' => $data['document']['templateId'],
                    'format' =>
                        isset($data['document']['format']) ?
                        $data['document']['format'] : 'rtf'
                    ]
            );
        }

        return $documentData;
    }

    protected function trimFormFields($data)
    {
        return $this->trimFields($data, array('csrf', 'submit', 'fields', 'form-actions'));
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
     * Check if a button was pressed
     *
     * @param string $button
     * @return bool
     */
    public function isButtonPressed($button)
    {
        $request = $this->getRequest();
        $data = (array)$request->getPost();

        return $request->isPost() && isset($data['form-actions'][$button]);
    }

    public function cancelButtonListener(MvcEvent $event)
    {
        $this->setupIndexRoute($event);
        $cancelResponse = $this->checkForCancelButton('cancel');
        if (!is_null($cancelResponse)) {
            $event->setResult($cancelResponse);
            return $cancelResponse;
        }
    }

    /**
     * This method needs some things.
     *
     * 1. A form element with the name of "cancel"
     *
     * @return \Zend\Http\Response
     */
    public function checkForCancelButton($buttonName = 'cancel')
    {
        if ($this->isButtonPressed($buttonName)) {

            $this->addInfoMessage('Action cancelled successfully');

            return $this->redirectToIndex();
        }
    }

    /**
     * Remove file
     *
     * @param int $id
     */
    protected function deleteFile($id, $fieldset, $name)
    {
        $fileDetails = $this->makeRestCall(
            'Document',
            'GET',
            array('id' => $id),
            array('properties' => array('identifier'))
        );

        if (isset($fileDetails['identifier']) && !empty($fileDetails['identifier'])) {
            if ($this->getUploader()->remove($fileDetails['identifier'])) {

                $this->makeRestCall('Document', 'DELETE', array('id' => $id));
                $fieldset->remove($name);
            }
        }
    }

    /**
     * Gets a view model with optional params
     *
     * @param array $params
     * @return ViewModel
     */
    public function getView(array $params = null)
    {
        return new ViewModel($params);
    }

    /**
     * Disable field validation
     *
     * @param \Zend\InputFilter\InputFilter $inputFilter
     * @return null
     */
    protected function disableValidation($inputFilter)
    {
        if ($inputFilter instanceof InputFilter) {
            foreach ($inputFilter->getInputs() as $input) {
                $this->disableValidation($input);
            }
            return;
        }

        if ($inputFilter instanceof Input) {
            $inputFilter->setAllowEmpty(true);
            $inputFilter->setRequired(false);
            $inputFilter->setValidatorChain(new ValidatorChain());
        }
    }

    /**
     * Disable all elements recursively
     *
     * @param \Zend\Form\Fieldset $elements
     * @return null
     */
    protected function disableElements($elements)
    {
        if ($elements instanceof Fieldset) {
            foreach ($elements->getElements() as $element) {
                $this->disableElements($element);
            }

            foreach ($elements->getFieldsets() as $fieldset) {
                $this->disableElements($fieldset);
            }
            return;
        }

        if ($elements instanceof Element) {
            $elements->setAttribute('disabled', 'disabled');
        }
    }

    /**
     * Get section service name
     *
     * @return string
     */
    protected function getSectionServiceName()
    {
        return $this->sectionServiceName;
    }

    /**
     * Sets the caught response
     *
     * @param mixed $response
     */
    protected function setCaughtResponse($response)
    {
        $this->caughtResponse = $response;
    }

    /**
     * Getter for caughtResponse
     *
     * @return mixed
     */
    protected function getCaughtResponse()
    {
        return $this->caughtResponse;
    }

    /**
     * Set the layout
     *
     * @param string $layout
     */
    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get the layout
     *
     * @param string $layout
     */
    protected function getLayout()
    {
        return $this->layout;
    }

    /**
     * Check if a form exists
     *
     * @param string $formName
     * @return boolean
     */
    protected function formExists($formName)
    {
        return file_exists($this->getFormLocation($formName));
    }

    /**
     * Get a form location
     *
     * @param string $formName
     * @return string
     */
    protected function getFormLocation($formName)
    {
        return $this->getServiceLocator()->get('Config')['forms_path'] . $formName . '.form.php';
    }

    /**
     * Complete section and save
     *
     * @todo Can't move this to section service just yet due to the dependency on getDataMap and the posibility of save
     *   being extended
     *
     * @param array $data
     * @return array
     */
    protected function processSave($data)
    {
        $data = $this->processDataMapForSave($data, $this->getDataMap());

        $response = $this->save($data);

        if ($response instanceof Response || $response instanceof ViewModel) {
            $this->setCaughtResponse($response);
            return;
        }

        return $response;
    }

    /**
     * Process save when we have a table form
     *
     * @param array $data
     */
    protected function processSaveCrud($data)
    {
        $oldData = $data;

        $data = $this->processDataMapForSave($data, $this->getDataMap());

        $response = $this->saveCrud($data);

        if ($response instanceof Response || $response instanceof ViewModel) {
            $this->setCaughtResponse($response);
            return;
        }

        $formTables = $this->getFormTables();

        foreach (array_keys($formTables) as $table) {

            if (!is_array($oldData[$table]['action'])) {
                $action = strtolower($oldData[$table]['action']);
                $id = isset($oldData[$table]['id']) ? $oldData[$table]['id'] : null;
            } else {
                $action = array_keys($oldData[$table]['action'])[0];
                $id = (isset($oldData[$table]['action'][$action])
                    ? array_keys($oldData[$table]['action'][$action])[0]
                    : null);
            }

            if (!empty($action)) {

                $routeAction = $action;

                if ($table !== 'table') {
                    $routeAction = $table . '-' . $action;
                }

                // Incase we want to try and hi-jack the crud action check
                if (method_exists($this, 'checkForAlternativeCrudAction')) {
                    $response = $this->checkForAlternativeCrudAction($routeAction);

                    if ($response instanceof Response) {
                        $this->setCaughtResponse($response);
                        return;
                    }
                }

                if ($action == 'add') {
                    $this->setCaughtResponse(
                        $this->redirectToRoute(null, array('action' => $routeAction), array(), true)
                    );
                    return;
                }

                if (empty($id)) {
                    $this->setCaughtResponse($this->crudActionMissingId());
                    return;
                }

                $options = array();
                $params = array('action' => $routeAction);

                if (is_array($id) && count($id) === 1) {
                    $id = $id[0];
                }

                if (is_array($id)) {
                    $options = array('query' => array('id' => $id));
                } else {
                    $params['id'] = $id;
                }

                $this->setCaughtResponse(
                    $this->redirectToRoute(
                        null,
                        $params,
                        $options,
                        true
                    )
                );
                return;
            }
        }
    }

    /**
     * Get the namespace parts
     *
     * @return array
     */
    public function getNamespaceParts()
    {
        $controller = get_called_class();

        return explode('\\', $controller);
    }

    /**
     * Convert dash to camel case
     *
     * @param string $string
     * @return string
     */
    protected function dashToCamel($string)
    {
        $converter = new DashToCamelCase();
        return $converter->filter($string);
    }

    /**
     * Return the section service
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getSectionService($sectionServiceName = null)
    {
        if ($sectionServiceName === null) {
            $sectionServiceName = $this->getSectionServiceName();
        }

        if (!isset($this->sectionServices[$sectionServiceName])) {
            $this->sectionServices[$sectionServiceName] = $this->getServiceLocator()
                ->get('SectionService')->getSectionService($sectionServiceName);

            $this->sectionServices[$sectionServiceName]->setIdentifier($this->getIdentifier());
            $this->sectionServices[$sectionServiceName]->setIsAction($this->isAction());
            $this->sectionServices[$sectionServiceName]->setActionId($this->getActionId());
            $this->sectionServices[$sectionServiceName]->setActionName($this->getActionName());
            $this->sectionServices[$sectionServiceName]->setRequest($this->getRequest());
        }

        return $this->sectionServices[$sectionServiceName];
    }

    /**
     * Get the journey identifier
     *
     * @return int
     */
    protected function getIdentifier()
    {
        if (empty($this->identifier)) {
            $this->identifier = $this->params()->fromRoute($this->getIdentifierName());
        }

        return $this->identifier;
    }

    /**
     * Default method to get the identifier name
     *
     * @return string
     */
    protected function getIdentifierName()
    {
        return $this->identifierName;
    }

    /**
     * Check if we have a sub action
     *
     * @return boolean
     */
    protected function isAction()
    {
        if (is_null($this->isAction)) {
            $action = $this->getActionName();
            $this->isAction = ($action != 'index');
        }

        return $this->isAction;
    }

    /**
     * Getter for action name
     *
     * @return string
     */
    protected function getActionName()
    {
        if (empty($this->actionName)) {
            $this->actionName = $this->params()->fromRoute('action');
        }

        return $this->actionName;
    }

    /**
     * Get the sub action id
     *
     * @return int
     */
    protected function getActionId()
    {
        if (empty($this->actionId)) {
            $this->actionId = $this->params()->fromRoute('id');

            $queryIds = $this->params()->fromQuery('id');

            if (empty($this->actionId) && !empty($queryIds)) {
                $this->actionId = $queryIds;
            }
        }

        return $this->actionId;
    }

    /**
     * Render the view
     *
     * @param ViewModel $view
     * @return ViewModel
     */
    protected function render($view)
    {
        return $view;
    }

    /**
     * Load data for the form
     *
     * This method should be overridden
     *
     * @param int $id
     * @return array
     */
    protected function load($id)
    {
        $results = $this->getSectionService()->load($id);

        if ($results === false) {
            $this->setCaughtResponse($this->notFoundAction());
            return;
        }

        return $results;
    }

    /**
     * Process the data map for saving
     *
     * @param type $data
     */
    protected function processDataMapForSave($oldData, $map = array(), $section = 'main')
    {
        return $this->getSectionService()->processDataMapForSave($oldData, $map, $section);
    }

    /**
     * Save crud data
     *
     * @param array $data
     * @return mixed
     */
    protected function saveCrud($data)
    {
        return $this->getSectionService()->saveCrud($data);
    }

    /**
     * Save data
     *
     * @todo Start using the section service version (This exists for backwards compat)
     *
     * @param array $data
     * @param string $service
     * @return array
     */
    protected function save($data, $service = null)
    {
        if (empty($service)) {
            $service = $this->getService();
        }

        return $this->getSectionService()->save($data, $service);
    }

    /**
     * Get the sub action service
     *
     * @todo use the section services version (modified for backwards compat for the time being)
     *
     * @return string
     */
    protected function getActionService()
    {
        if ($this->actionService === null) {
            $this->actionService = $this->getSectionService()->getActionService();
        }

        return $this->actionService;
    }

    /**
     * Gets the data map
     *
     * @todo use the section service version (This is included for backwards compat)
     *
     * @return array
     */
    protected function getDataMap()
    {
        if ($this->dataMap === null) {
            $this->dataMap = $this->getSectionService()->getDataMap();
        }

        return $this->dataMap;
    }

    /**
     * Getter for data bundle
     *
     * @todo use the section service version (This is included for backwards compat)
     *
     * @return array
     */
    protected function getDataBundle()
    {
        if ($this->dataBundle === null) {
            $this->dataBundle = $this->getSectionService()->getDataBundle();
        }

        return $this->dataBundle;
    }

    /**
     * Get the service name
     *
     * @todo use the section service version (This is included for backwards compat)
     *
     * @return string
     */
    protected function getService()
    {
        if ($this->service === null) {
            $this->service = $this->getSectionService()->getService();
        }

        return $this->service;
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        if ($this->formTables === null) {
            $this->formTables = $this->getSectionService()->getFormTables();
        }

        return $this->formTables;
    }

    /**
     * Map the data on load
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        return $this->getSectionService()->processLoad($data);
    }

    /**
     * Delete
     *
     * @return Response
     */
    protected function delete($id = null, $service = null)
    {
        if ($id === null) {
            $id = $this->getActionId();
        }

        if ($service === null) {
            $service = $this->getActionService();
        }

        return $this->getSectionService()->delete($id, $service);
    }
}
