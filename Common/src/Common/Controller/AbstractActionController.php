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
use Zend\Form\Form;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\Validator\ValidatorChain;
use Zend\Validator\File\FilesSize;
use Zend\Filter\Word\DashToCamelCase;
use Common\Form\Elements\Types\Address;

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @todo Need to move as much business logic as possible into services
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
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
     * Holds the section service name
     *
     * @var string
     */
    protected $sectionServiceName;

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

    private $fieldValues = [];
    private $persist = true;
    protected $enableCsrf = true;
    protected $validateForm = true;

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
     * @param \Zend\Mvc\MvcEvent $e
     */
    private function setupIndexRoute(MvcEvent $e)
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
    private function preOnDispatch()
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
     * @todo this shouldn't be public
     *
     * @return mixed
     */
    public function redirectToIndex()
    {
        return call_user_func_array([$this->redirect(), 'toRoute'], $this->indexRoute);
    }

    /**
     * Set navigation for breadcrumb
     *
     * @todo this shouldn't be public
     *
     * @param array $navRoutes
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
     *
     * @todo this shouldn't be public
     *
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

    /**
     * Get all params
     *
     * @todo this shouldn't be public
     *
     * @return type
     */
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
     * @todo This method shouldn't be public
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
     * @todo This method shouldn't be public, and shouldn't exist
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
     * @todo This method shouldn't be public, and shouldn't exist
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
     * @todo This method shouldn't be public, and shouldn't exist
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
     * @todo This method shouldn't be public, and shouldn't exist
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
     * @todo This method shouldn't be public, and shouldn't exist
     *
     * @param string $name
     * @return string
     */
    public function getFromPost($name)
    {
        return $this->params()->fromPost($name);
    }

    /**
     * Get logged in user
     *
     * @todo This method shouldn't be public
     *
     * @return int
     */
    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }

    /**
     * Set logged in user
     *
     * @todo This method shouldn't be public
     *
     * @param int $id
     * @return \Common\Controller\AbstractActionController
     */
    public function setLoggedInUser($id)
    {
        $this->loggedInUser = $id;

        return $this;
    }

    /**
     * Get uploader
     *
     * @todo This method shouldn't be public
     *
     * @return object
     */
    public function getUploader()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getUploader();
        }

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

    /**
     * Attach default listeneers
     */
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        if ($this instanceof CrudInterface) {
            $this->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, array($this, 'cancelButtonListener'), 100);
        }
    }

    /**
     * Allow csrf to be enabled and disabled
     *
     * @todo This method shouldn't be public
     *
     * @param boolean $boolean
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
        if ($this->getSectionServiceName() !== null) {
            $this->getSectionService()->setPersist($persist);
        } else {
            $this->persist = $persist;
        }
    }

    /**
     * Get persist
     *
     * @return boolean
     */
    protected function getPersist()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getPersist();
        }

        return $this->persist;
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
        if ($this->getSectionServiceName() !== null) {
            $this->getSectionService()->setFieldValue($key, $value);
        } else {
            $this->fieldValues[$key] = $value;
        }
    }

    /**
     * Getter for field values
     *
     * @return array
     */
    protected function getFieldValues()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getFieldValues();
        }

        return $this->fieldValues;
    }

    /**
     * Normalise form name
     *
     * @param string $name
     * @param boolean $ucFirst
     * @return string
     */
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
     * Get form class
     *
     * @todo Turn this into a proper service/factory for forms
     *
     * @param string $type
     * @return \Zend\Form\Form
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
     *
     * @param string $type
     * @return \Zend\Form\Form
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

        $form = $this->processPostcodeLookup($form);

        return $form;
    }

    /**
     * Process the postcode lookup functionality
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
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

                    $this->setPersist(false);

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

                    $this->setPersist(false);

                    $address = $this->getAddressForUprn($post[$name]['searchPostcode']['addresses']);

                    $removeSelectFields = true;

                    $addressDetails = $this->getAddressService()->formatPostalAddressFromBs7666($address);

                    $this->setFieldValue($name, array_merge($post[$name], $addressDetails));

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

    /**
     * Get Addresses For Postcode
     *
     * @param string $postcode
     * @return array
     */
    private function getAddressesForPostcode($postcode)
    {
        return $this->sendGet('postcode\address', array('postcode' => $postcode), true);
    }

    /**
     * Get address for uprn
     *
     * @param string $uprn
     * @return array
     */
    private function getAddressForUprn($uprn)
    {
        return $this->sendGet('postcode\address', array('id' => $uprn), true);
    }

    /**
     * Get address service
     *
     * @return object
     */
    private function getAddressService()
    {
        return $this->getServiceLocator()->get('address');
    }

    /**
     * Alter form before validation
     *
     * @NOTE stub method, should be overridden where needed
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterFormBeforeValidation($form)
    {
        return $form;
    }

    /**
     * Method to process posted form data and validate it and process a callback
     *
     * @todo This method shouldn't be public
     *
     * @param \Zend\Form\Form $form
     * @param callable $callback
     * @return \Zend\Form\Form
     */
    public function formPost($form, $callback = null, $additionalParams = array())
    {
        if (!$this->enableCsrf) {
            $form->getInputFilter()->remove('csrf');
            $form->remove('csrf');
        }

        $form = $this->alterFormBeforeValidation($form);

        if ($this->getRequest()->isPost()) {

            $data = array_merge((array)$this->getRequest()->getPost(), $this->getFieldValues());

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

                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'params' => $additionalParams
                ];

                $this->callCallbackIfExists($callback, $params);
            }
        }

        return $form;
    }

    /**
     * Calls the callback function/method if exists.
     *
     * @todo This method shouldn't be public
     *
     * @param callable $callback
     * @param array $params
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
     * @todo This method shouldn't be public
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
     * @todo This method shouldn't be public
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
     * @param \Zend\Form\ElementInterface $form
     */
    private function disableEmptyValidation($form)
    {
        foreach ($form->getElements() as $key => $element) {

            $value = $element->getValue();
            if (empty($value)) {
                $form->getInputFilter()->get($key)->setAllowEmpty(true);
                $form->getInputFilter()->get($key)->setValidatorChain(new ValidatorChain());
            }
        }

        foreach ($form->getFieldsets() as $key => $fieldset) {
            foreach ($fieldset->getElements() as $elementKey => $element) {

                $value = $element->getValue();

                if (empty($value)) {
                    $form->getInputFilter()->get($key)->get($elementKey)->setAllowEmpty(true);
                    $form->getInputFilter()->get($key)->get($elementKey)->setValidatorChain(new ValidatorChain());
                }
            }
        }

        return $form;
    }

    /**
     * Process add
     *
     * @todo this shouldnt be public, and should live in a service OR we should look at using save instead
     *
     * @param array $data
     * @param string $entityName
     * @return array
     */
    public function processAdd($data, $entityName)
    {
        $data = $this->trimFormFields($data);

        $result = $this->makeRestCall($entityName, 'POST', $data);

        $data['id'] = $result['id'];
        $this->generateDocument($data);

        return $result;
    }

    /**
     * Process edit
     *
     * @todo this shouldnt be public, and should live in a service OR we should look at using save instead
     *
     * @param array $data
     * @param string $entityName
     * @return array
     */
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
     * @todo this needs moving into a service
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

    /**
     * Trim form fields
     *
     * @todo if we move processAdd and processEdit OR use Save instead, we need to remove this
     *
     * @param array $data
     * @return array
     */
    protected function trimFormFields($data)
    {
        $unwantedFields = array('csrf', 'submit', 'fields', 'form-actions');

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
     * @todo this method shouldn't be public
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

    /**
     * Cancel button listener
     *
     * @todo this method shouldn't be public (As it is used as a callback, maybe it should live somewhere else)
     *
     * @param MvcEvent $event
     * @return mixed
     */
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
     * @todo same as above
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
        if ($this->getSectionServiceName() !== null) {
            $this->getSectionService()->deleteFile($id, $fieldset, $name);
        } else {
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
    }

    /**
     * Gets a view model with optional params
     *
     * @todo this method shouldn't be public, and shouldn't exist
     *
     * @param array $params
     * @return ViewModel
     */
    public function getView(array $params = null)
    {
        return new ViewModel($params);
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
     * @todo Can't move this method to the service yet due to dependencies
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
     * @todo this method shouldn't be public
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
        if (empty($this->loadedData)) {
            if ($this->getSectionServiceName() !== null) {
                $this->loadedData = $this->getSectionService()->load($id);
            } else {

                $service = $this->getService();

                $result = $this->makeRestCall($service, 'GET', $id, $this->getDataBundle());

                $this->loadedData = $result;
            }
        }

        if ($this->loadedData === false) {
            $this->setCaughtResponse($this->notFoundAction());
            return;
        }

        return $this->loadedData;
    }

    /**
     * Process the data map for saving
     *
     * @param type $data
     */
    public function processDataMapForSave($oldData, $map = array(), $section = 'main')
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processDataMapForSave($oldData, $map, $section);
        }

        if (empty($map)) {
            return $oldData;
        }

        if (isset($map['_addresses'])) {
            foreach ($map['_addresses'] as $address) {
                $oldData = $this->processAddressData($oldData, $address);
            }
        }

        $data = array();
        if (isset($map[$section]['mapFrom'])) {
            foreach ($map[$section]['mapFrom'] as $key) {
                if (isset($oldData[$key])) {
                    $data = array_merge($data, $oldData[$key]);
                }
            }
        }

        if (isset($map[$section]['children'])) {
            foreach ($map[$section]['children'] as $child => $options) {
                $data[$child] = $this->processDataMapForSave($oldData, array($child => $options), $child);
            }
        }

        if (isset($map[$section]['values'])) {
            $data = array_merge($data, $map[$section]['values']);
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

        $data[$addressName]['countryCode'] = $data[$addressName]['countryCode'];

        $data['addresses'][$addressName] = $data[$addressName];

        unset($data[$addressName]);

        return $data;
    }

    /**
     * Save crud data
     *
     * @param array $data
     * @return mixed
     */
    protected function saveCrud($data)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->saveCrud($data);
        }

        return $this->save($data);
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

        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->save($data, $service);
        }

        $method = 'POST';

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        }

        return $this->makeRestCall($service, $method, $data);
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionService();
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getDataMap();
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getDataBundle();
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getService();
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getFormTables();
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
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processLoad($data);
        }

        return $data;
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

        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->delete($id, $service);
        }

        if ($service === null) {
            $service = $this->getService();
        }

        if (!empty($id) && !empty($service)) {

            $this->makeRestCall($service, 'DELETE', array('id' => $id));

            return true;
        }

        return false;
    }

    /**
     * Added extra method called after setting form data
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function postSetFormData($form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->postSetFormData($form);
        }

        return $form;
    }

    /**
     * Process file uploads
     *
     * @param array $uploads
     * @param Form $form
     * @return array
     */
    protected function processFileUploads($uploads, $form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processFileUploads($uploads, $form);
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $files = $this->getRequest()->getFiles()->toArray();

            return $this->processFileUpload($uploads, $post, $files, $form);
        }

        return array();
    }

    /**
     * Process a single file upload
     *
     * @param array $uploads
     * @param array $data
     * @param array $files
     * @param Form $form
     * @return array
     */
    private function processFileUpload($uploads, $data, $files, $form)
    {
        $responses = array();

        foreach ($uploads as $fieldset => $callback) {

            if ($form->has($fieldset)) {
                $form = $form->get($fieldset);

                if (is_array($callback)) {

                    $responses[$fieldset] = $this->processFileUpload(
                        $callback,
                        $data[$fieldset],
                        $files[$fieldset],
                        $form
                    );

                } elseif (isset($data[$fieldset]['file-controls']['upload'])
                    && !empty($data[$fieldset]['file-controls']['upload'])
                ) {

                    $this->setPersist(false);

                    $error = $files[$fieldset]['file-controls']['file']['error'];

                    $validator = $this->getFileSizeValidator();

                    if ($error == UPLOAD_ERR_OK
                        && !$validator->isValid($files[$fieldset]['file-controls']['file']['tmp_name'])
                    ) {
                        $error = UPLOAD_ERR_INI_SIZE;
                    }

                    $responses[$fieldset] = $error;

                    switch ($error) {
                        case UPLOAD_ERR_OK:
                            $responses[$fieldset] = call_user_func(
                                array($this, $callback),
                                $files[$fieldset]['file-controls']['file']
                            );
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $form->setMessages(
                                array('__messages__' => array('File was only partially uploaded'))
                            );
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $form->setMessages(
                                array('__messages__' => array('Please select a file to upload'))
                            );
                            break;
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $form->setMessages(
                                array('__messages__' => array('The file was too large to upload'))
                            );
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                        case UPLOAD_ERR_CANT_WRITE:
                        case UPLOAD_ERR_EXTENSION:
                            $form->setMessages(
                                array('__messages__' => array('An unexpected error occurred while uploading the file'))
                            );
                            break;
                    }
                }
            }
        }
        return $responses;
    }

    /**
     * Get filesize validator
     *
     * @return \Zend\Validator\File\FilesSize
     */
    public function getFileSizeValidator()
    {
        return new FilesSize('2MB');
    }

    /**
     * Process file deletions
     *
     * @param array $uploads
     * @param Form $form
     * @return array
     */
    public function processFileDeletions($uploads, $form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processFileDeletions($uploads, $form);
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            return $this->processFileDeletion($uploads, $post, $form);
        }

        return array();
    }

    /**
     * Process a single file deletion
     *
     * @param array $uploads
     * @param array $data
     * @param Form $form
     * @return array
     */
    private function processFileDeletion($uploads, $data, $form)
    {
        $responses = array();

        foreach ($uploads as $fieldset => $callback) {

            if ($form->has($fieldset)) {
                $form = $form->get($fieldset);

                if (is_array($callback)) {

                    $responses[$fieldset] = $this->processFileDeletion(
                        $callback,
                        $data[$fieldset],
                        $form
                    );

                } else {

                    foreach ($form->get('list')->getFieldsets() as $listFieldset) {

                        $name = $listFieldset->getName();

                        if (isset($data[$fieldset]['list'][$name]['remove'])
                            && !empty($data[$fieldset]['list'][$name]['remove'])) {

                            // @todo sort this
                            $this->setPersist(false);

                            $responses[$fieldset] = call_user_func(
                                array($this, $callback),
                                $data[$fieldset]['list'][$name]['id'],
                                $form->get('list'),
                                $name
                            );
                        }
                    }
                }
            }
        }
        return $responses;
    }
}
