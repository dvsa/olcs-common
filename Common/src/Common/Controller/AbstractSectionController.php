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
abstract class AbstractSectionController extends AbstractController
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
     * Holds any inline scripts for the current page
     *
     * @var array
     */
    protected $inlineScripts = [];

    /**
     * Getter for action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
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

            return $this->formName . '-sub-action';
        }

        return $this->formName;
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
     * Default method to get form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
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
        return $this->actionService;
    }

    /**
     * Gets the action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
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
        return $this->load($this->getIdentifier());
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
        if ($this->isAction()) {

            $action = $this->getActionName();

            if (strstr($action, '-')) {
                $splitted = explode('-', $action);
                $action = count($splitted) ? $splitted[count($splitted) - 1] : '';
            }

            if ($action === 'edit') {

                $data = $this->actionLoad($this->getActionId());
            } else {
                $data = array();
            }

            return $this->processActionLoad($data);
        }

        return $this->processLoad($this->loadCurrent());
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
            $action = $this->getActionName();

            if (strstr($action, '-')) {
                list($prefix, $action) = explode('-', $action);
                unset($prefix);
            }

            if ($action == 'edit') {
                $form->get('form-actions')->remove('addAnother');
            }
        }

        $alterMethod = $this->getAlterFormMethod();

        return $this->$alterMethod($form);
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
        unset($form);

        $data = $this->processDataMapForSave($data, $this->getActionDataMap());

        $response = $this->actionSave($data);

        if ($response instanceof Response || $response instanceof ViewModel) {
            $this->setCaughtResponse($response);
            return;
        }

        $this->setCaughtResponse($this->postActionSave());
    }

    /**
     * Process loading the sub section data
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
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
        if (empty($this->actionData)) {

            $this->actionData = $this->makeRestCall(
                $this->getActionService(),
                'GET',
                array('id' => $id),
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
     * Get the inline scripts
     *
     * @return array
     */
    protected function getInlineScripts()
    {
        return $this->inlineScripts;
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

            $this->addInfoMessage('Your changes have been discarded');

            return $this->goBackToSection();
        }
    }

    /**
     * Delete
     *
     * @return Response
     */
    protected function delete($id = null, $service = null)
    {
        $service = $this->getActionService();
        $id = $this->getActionId();

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
        }

        return $this->actionId;
    }
}
