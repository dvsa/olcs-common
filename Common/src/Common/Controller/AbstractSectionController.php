<?php

/**
 * Abstract Section Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Controller;

use Zend\InputFilter\InputFilter;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Abstract Section Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSectionController extends AbstractActionController
{
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
     * Holds the form name
     *
     * @var string
     */
    protected $formName;

    /**
     * Holds the form callback
     *
     * @var string
     */
    protected $formCallback = 'processSave';

    /**
     * Holds the form callback
     *
     * @var string
     */
    protected $actionFormCallback = 'processActionSave';

    /**
     * Holds the alter form method name
     *
     * @var string
     */
    protected $alterFormMethod = 'alterForm';


    /**
     * Holds the alter action form method name
     *
     * @var string
     */
    protected $alterActionFormMethod = 'alterActionForm';

    /**
     * Holds the action name
     *
     * @var string
     */
    private $actionName;

    /**
     * Holds the isAction
     *
     * @var boolean
     */
    protected $isAction;

    /**
     * These actions will attempt to look for a bespoke form/table/view etc
     *
     * @var array
     */
    protected $bespokeSubActions = array();

    /**
     * Here we can add sub actions that are always bespoke
     *
     * @var array
     */
    protected $defaultBespokeSubActions = array(
        'delete'
    );

    /**
     * Cache the action suffix
     *
     * @var string
     */
    protected $actionSuffix;

    /**
     * Action data map
     *
     * @var array
     */
    protected $actionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    /**
     * Holds the action service name
     *
     * @var string
     */
    protected $actionService = null;

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = null;

    /**
     * Holds the action data
     *
     * @var array
     */
    protected $actionData;

    /**
     * Holds the table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Holds hasTable
     *
     * @var boolean
     */
    protected $hasTable = null;

    /**
     * Holds hasForm
     *
     * @var boolean
     */
    protected $hasForm = null;

    /**
     * Holds the action id
     *
     * @var int
     */
    protected $actionId;

    /**
     * Current messages
     *
     * @var array
     */
    protected $currentMessages = array(
        'default' => array(),
        'error' => array(),
        'info' => array(),
        'warning' => array(),
        'success' => array()
    );

    /**
     * Get bespoke sub actions
     *
     * @return array
     */
    protected function getBespokeSubActions()
    {
        return $this->bespokeSubActions;
    }

    /**
     * Merge the defined bespokeSubActions, with the defaults
     *
     * @return array
     */
    protected function getAllBespokeSubActions()
    {
        return array_merge($this->getBespokeSubActions(), $this->defaultBespokeSubActions);
    }

    /**
     * Getter for action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionDataBundle();
        }

        return $this->actionDataBundle;
    }

    /**
     * Get form name
     *
     * @return string
     */
    protected function getFormName()
    {
        if ($this->isAction()) {

            $action = $this->getActionFromFullActionName();

            // @NOTE The delete bespoke action is a special case
            //  and we want a generic delete confirmation form
            if ($action == 'delete' && $this->isBespokeAction()) {
                return 'generic-delete-confirmation';
            }

            return $this->formName . $this->getSuffixForCurrentAction();
        }

        return $this->formName;
    }

    /**
     * Get form/table/view suffix for current action
     *
     * @return string
     */
    protected function getSuffixForCurrentAction()
    {
        if ($this->actionSuffix == null) {

            $action = $this->getActionFromFullActionName();

            $this->actionSuffix = $this->getSuffixForAction($action);
        }

        return $this->actionSuffix;
    }

    /**
     * Get suffix for action
     *
     * @param string $action
     * @return string
     */
    protected function getSuffixForAction($action)
    {
        if ($this->isBespokeAction()) {
            return '-' . $action;
        }

        return '-sub-action';
    }

    /**
     * Get the form callback
     *
     * @return string
     */
    protected function getFormCallback()
    {
        if ($this->isAction()) {
            return $this->actionFormCallback;
        }

        return $this->formCallback;
    }

    /**
     * Check if an action is bespoke
     * @return type
     */
    protected function isBespokeAction($action = null)
    {
        if ($action == null) {
            $action = $this->getActionFromFullActionName();
        }

        return in_array($action, $this->getAllBespokeSubActions());
    }

    /**
     * Default method to get form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getFormTableData($id, $table);
        }

        return array();
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
     * Determine the alter form method name
     *
     * @return string
     */
    protected function getAlterFormMethod()
    {
        if ($this->isAction()) {

            if ($this->isBespokeAction()) {
                $action = $this->getActionFromFullActionName();
                return 'alter' . ucfirst($action) . 'Form';
            }

            return $this->alterActionFormMethod;
        }

        return $this->alterFormMethod;
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
     * Get the sub action service
     *
     * @return string
     */
    protected function getActionService()
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionService();
        }

        return $this->actionService;
    }

    /**
     * Gets the action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionDataMap();
        }

        return $this->actionDataMap;
    }

    /**
     * Format the form table configs
     *
     * @param array $formTables
     * @return array
     */
    protected function formatFormTableConfigs($formTables)
    {
        $tableConfigs = array();

        foreach ($formTables as $table => $config) {
            $tableConfigs[$table] = array(
                'config' => $config,
                'data' => $this->getFormTableData($this->getIdentifier(), $table)
            );
        }

        return $tableConfigs;
    }

    /**
     * Moved the get form logic so it can be re-used
     *
     * @return Form
     */
    protected function getNewForm()
    {
        $formTables = $this->getFormTables();

        if ($this->isAction() || empty($formTables)) {

            return $this->generateFormWithData($this->getFormName(), $this->getFormCallback(), $this->getDataForForm());
        }

        return $this->generateTableFormWithData(
            $this->getFormName(),
            array(
                'success' => $this->getFormCallback(),
                'crud_action' => $this->getFormCallback() . 'Crud'
            ),
            $this->getDataForForm(),
            $this->formatFormTableConfigs($formTables)
        );
    }

    /**
     * Get data for form
     *
     * @return array
     */
    protected function getDataForForm()
    {
        $data = array();

        if (!$this->getRequest()->isPost()) {
            $data = $this->getFormData();
        }

        return $data;
    }

    /**
     * Simple helper method to load the current application
     *
     * @return array
     */
    protected function loadCurrent()
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->loadCurrent();
        }

        return $this->load($this->getIdentifier());
    }

    /**
     * @return array
     */
    protected function getFormDefaults()
    {
        return [];
    }

    /**
     * Get the form data
     *
     * This is not in the method above as it can be overridden independantly
     *
     * @return array
     */
    protected function getFormData()
    {
        if ($this->isBespokeAction()) {

            $action = $this->getActionFromFullActionName();
            return $this->{$action . 'Load'}($this->getActionId());
        }

        if ($this->isAction()) {

            $action = $this->getActionFromFullActionName();

            if ($action === 'edit') {

                $data = $this->actionLoad($this->getActionId());
            } else {
                $data = $this->getFormDefaults();
            }

            return $this->processActionLoad($data);
        }

        return $this->processLoad($this->loadCurrent());
    }

    /**
     * Get the last part of the action from the action name
     *
     * @param string $action
     * @return string
     */
    protected function getActionFromFullActionName($action = null)
    {
        if ($action == null) {
            $action = strtolower($this->getActionName());
        }

        return parent::getActionFromFullActionName($action);
    }

    /**
     * Alter the form before validation
     *
     * @param Form $form
     * @return Form
     */
    protected function alterFormBeforeValidation($form)
    {
        if ($this->isAction()) {
            $action = $this->getActionFromFullActionName();

            if ($action == 'edit') {
                $form->get('form-actions')->remove('addAnother');
            }
        }

        $alterMethod = $this->getAlterFormMethod();

        if (method_exists($this, $alterMethod)) {
            return $this->$alterMethod($form);
        }

        return $form;
    }

    /**
     * Alter the form
     *
     * This method should be overridden
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterForm($form);
        }

        return $form;
    }

    /**
     * Alter the action form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterActionForm($form)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterActionForm($form);
        }

        return $form;
    }

    /**
     * Set fields as not required
     *
     * @param \Zend\InputFilter\InputFilter $inputFilter
     */
    protected function setFieldsAsNotRequired($inputFilter)
    {
        $inputs = $inputFilter->getInputs();

        foreach ($inputs as $input) {
            if ($input instanceof InputFilter) {
                $input = $this->setFieldsAsNotRequired($input);
            } else {
                $input->setRequired(false);
                $input->setAllowEmpty(true);
            }
        }

        return $inputs;
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
     * Save the sub action
     *
     * @param array $data
     * @return array
     */
    protected function processActionSave($data, $form)
    {
        if ($this->isBespokeAction()) {
            $action = $this->getActionFromFullActionName();

            $method = $action . 'Save';
        } else {
            $data = $this->getHelperService('DataMapHelper')->processDataMap($data, $this->getActionDataMap());

            $method = 'actionSave';
        }

        if ($this->shouldSkipActionSave($data, $form)) {
            return;
        }

        $response = $this->$method($data);

        if ($response instanceof Response || $response instanceof ViewModel) {
            $this->setCaughtResponse($response);
            return;
        }

        $this->setCaughtResponse($this->postActionSave());
    }

    /**
     * Added a callback to call pre-actionSave
     *
     * @param array $data
     * @param form $form
     * @return boolean
     */
    protected function shouldSkipActionSave($data, $form)
    {
        return false;
    }

    /**
     * Process loading the sub section data
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processActionLoad($data);
        }

        return $data;
    }

    /**
     * Load sub section data
     *
     * @param int $id
     * @return array
     */
    protected function actionLoad($id)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->actionLoad($id);
        }

        if (empty($this->actionData)) {

            $this->actionData = $this->makeRestCall(
                $this->getActionService(),
                'GET',
                $id,
                $this->getActionDataBundle()
            );
        }

        return $this->actionData;
    }

    /**
     * Save sub action data
     *
     * @param array $data
     */
    protected function actionSave($data, $service = null)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->actionSave($data, $service);
        }

        $method = 'POST';

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        }

        if (is_null($service)) {
            $service = $this->getActionService();
        }

        return $this->makeRestCall($service, $method, $data);
    }

    /**
     * Post action save (Decide where to go)
     *
     * @return Response
     */
    protected function postActionSave()
    {
        if ($this->isButtonPressed('addAnother')) {
            return $this->goBackToAddAnother();
        }

        return $this->goBackToSection();
    }

    /**
     * Go back to sub action
     *
     * @return Response
     */
    protected function goBackToAddAnother()
    {
        return $this->redirect()->toRoute(null, array(), array(), true);
    }

    /**
     * Optionally add scripts to view, if there are any
     *
     * @param ViewModel $view
     */
    protected function maybeAddScripts($view)
    {
        $scripts = $this->getInlineScripts();

        if (empty($scripts)) {
            return;
        }

        // this process defers to a service which takes care of checking
        // whether the script(s) exist
        $view->setVariable('scripts', $this->loadScripts($scripts));
    }

    /**
     * Redirect to sub section
     *
     * @return Response
     */
    protected function goBackToSection()
    {
        return $this->redirect()->toRoute(
            null,
            array('action' => 'index', $this->getIdentifierName() => $this->getIdentifier())
        );
    }

    /**
     * Check for redirect
     *
     * @return Response
     */
    protected function checkForRedirect()
    {
        $crudAction = $this->checkForCrudAction();

        if ($crudAction instanceof Response || $crudAction instanceof ViewModel) {
            return $crudAction;
        }

        if ($this->isButtonPressed('cancel')) {

            $action = $this->getActionFromFullActionName();

            // This message isn't right for cancelling a delete action
            if ($action != 'delete') {

                $this->addInfoMessage('flash-discarded-changes');
            }

            return $this->goBackToSection();
        }
    }

    /**
     * Add current message
     *
     * @param string $message
     * @param string $namespace
     */
    protected function addCurrentMessage($message, $namespace = 'default')
    {
        $this->currentMessages[$namespace][] = $message;
    }

    /**
     * Attach messages to display in the current response
     */
    protected function attachCurrentMessages()
    {
        foreach ($this->currentMessages as $namespace => $messages) {
            foreach ($messages as $message) {
                $this->addMessage($message, $namespace);
            }
        }
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

        if (parent::delete($id, $service)) {

            return $this->goBackToSection();
        }

        return $this->notFoundAction();
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
     * Add table to a view
     * @param type $view
     */
    protected function maybeAddTable($view)
    {
        if ($this->hasTable() && $view->getVariable('table') == null) {

            $tableName = $this->getTableName();

            if (!empty($tableName)) {

                $data = $this->getTableData($this->getIdentifier());

                $settings = $this->getTableSettings();

                $table = $this->alterTable($this->getTable($tableName, $data, $settings));

                $view->setVariable('table', $table);
            }
        }
    }

    /**
     * Alter table
     *
     * @param object $table
     * @return object
     */
    protected function alterTable($table)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterTable($table);
        }

        return $table;
    }

    /**
     * Get table name
     *
     * @return string
     */
    protected function getTableName()
    {
        if ($this->isAction()) {

            return $this->tableName . $this->getSuffixForCurrentAction();
        }

        return $this->tableName;
    }

    /**
     * Check if the current sub section has a table
     *
     * @return boolean
     */
    protected function hasTable()
    {
        if (is_null($this->hasTable)) {

            $tableName = $this->getTableName();

            $this->hasTable = false;

            foreach ($this->getServiceLocator()->get('Config')['tables']['config'] as $location) {

                if (file_exists($location . $tableName . '.table.php')) {
                    $this->hasTable = true;
                    break;
                }
            }
        }

        return $this->hasTable;
    }

    /**
     * Render the section
     *
     * @return Response
     */
    protected function renderSection($view = null, $params = array())
    {
        $redirect = $this->checkForRedirect();

        if ($redirect instanceof Response || $redirect instanceof ViewModel) {
            return $redirect;
        }

        $view = $this->setupView($view, $params);

        $this->maybeAddTable($view);

        $response = $this->maybeAddForm($view);

        if ($response instanceof Response || $response instanceof ViewModel) {
            return $response;
        }

        $this->maybeAddScripts($view);

        $view = $this->preRender($view);

        $this->attachCurrentMessages();

        return $this->render($view);
    }

    /**
     * Setup the view for renderring
     *
     * @param ViewModel $view
     * @return ViewModel
     */
    protected function setupView($view = null, $params = array())
    {
        if (empty($view)) {
            $view = $this->getViewModel($params);
        }

        if ($view->getTemplate() == null) {
            $view->setTemplate($this->getViewTemplateName());
        }

        return $view;
    }

    /**
     * Potentially add a form
     *
     * @param ViewModel $view
     * @return Response
     */
    protected function maybeAddForm($view)
    {
        if ($this->hasForm() && $view->getVariable('form') == null) {

            $form = $this->getNewForm();

            $response = $this->getCaughtResponse();

            if ($response instanceof Response || $response instanceof ViewModel) {
                return $response;
            }

            $view->setVariable('form', $form);
        }
    }

    /**
     * Pre render
     *
     * @param ViewModel $view
     * @return ViewModel
     */
    protected function preRender($view)
    {
        return $view;
    }

    /**
     * Check if the current sub section has a form
     *
     * @return boolean
     */
    protected function hasForm()
    {
        if (is_null($this->hasForm)) {

            $this->hasForm = $this->formExists($this->getFormName());
        }

        return $this->hasForm;
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
     * Default Load data for the delete confirmation form
     *
     * @param int $id
     * @return array
     */
    protected function deleteLoad($id)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->deleteLoad($id);
        }

        if (is_array($id)) {
            $id = implode(',', $id);
        }

        return array('data' => array('id' => $id));
    }

    /**
     * Default Process delete
     *
     * @param array $data
     */
    protected function deleteSave($data)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            $this->getSectionService()->alterDeleteForm($data);
        } else {
            $ids = explode(',', $data['data']['id']);

            foreach ($ids as $id) {
                parent::delete($id, $this->getActionService());
            }
        }

        return $this->goBackToSection();
    }

    /**
     * Alter delete form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterDeleteForm($form)
    {
        // @NOTE progressivly start to include the logic within a service
        //  but maintain the old logic for backwards compatability
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterDeleteForm($form);
        }

        return $form;
    }

    /**
     * Return the section service
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getSectionService($sectionServiceName = null)
    {
        if (!isset($this->sectionServices[$sectionServiceName])) {
            $this->sectionServices[$sectionServiceName] = parent::getSectionService($sectionServiceName);

            // Configure the service for a section
            $this->sectionServices[$sectionServiceName]->setIdentifier($this->getIdentifier());
            $this->sectionServices[$sectionServiceName]->setIsAction($this->isAction());
            $this->sectionServices[$sectionServiceName]->setActionId($this->getActionId());
            $this->sectionServices[$sectionServiceName]->setActionName($this->getActionName());
            $this->sectionServices[$sectionServiceName]->setRequest($this->getRequest());
        }

        return $this->sectionServices[$sectionServiceName];
    }
}
