<?php

/**
 * Abstract Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

use Common\Util;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\View\Model\ViewModel;
use Zend\Validator\File\FilesSize;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Section Service
 *
 * @todo This is essentially a dumping ground from abstractSectionController we may want to re-factor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSectionService implements SectionServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        Util\RestCallTrait,
        Util\HelperServiceAware;

    /**
     * Holds the field values
     *
     * @var array
     */
    private $fieldValues = array();

    /**
     * Hold the identifier
     *
     * @var int
     */
    protected $identifier;

    /**
     * Holds whether the section is an action
     *
     * @var boolean
     */
    protected $isAction;

    /**
     * Holds the actionId
     *
     * @var int
     */
    protected $actionId;

    /**
     * Holds the action name
     *
     * @var string
     */
    protected $actionName;

    /**
     * Holds the request
     *
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * Holds the service
     *
     * @var string
     */
    protected $service;

    /**
     * Action service
     *
     * @var string
     */
    protected $actionService;

    /**
     * Holds the action data bundle
     *
     * @var array
     */
    protected $actionDataBundle;

    /**
     * Holds the action data
     *
     * @var array
     */
    protected $actionData;

    /**
     * Holds the action data map
     *
     * @var array
     */
    protected $actionDataMap;

    /**
     * Holds the data map
     *
     * @var array
     */
    protected $dataMap;

    /**
     * Holds the form tables
     *
     * @var array
     */
    protected $formTables;

    /**
     * Holds the dataBundle
     *
     * @var array
     */
    protected $dataBundle;

    /**
     * Holds the tableDataBundle
     *
     * @var array
     */
    protected $tableDataBundle;

    /**
     * Cache the factory instance
     *
     * @var \Common\Controller\Service\SectionServiceFactory
     */
    protected $sectionServiceFactory;

    /**
     * Cache the section services
     *
     * @var array
     */
    protected $sectionServices = array();

    /**
     * Holds the loaded data
     *
     * @var array
     */
    protected $loadedData;

    /**
     * @todo Need a better way to sort this, but basically need to tell the controller to not persist
     *
     * @var boolean
     */
    protected $persist = true;

    /**
     * Set persist
     *
     * @param boolean $persist
     */
    public function setPersist($persist = true)
    {
        $this->persist = $persist;
    }

    /**
     * Getter for persist
     *
     * @return boolean
     */
    public function getPersist()
    {
        return $this->persist;
    }

    /**
     * Set the identifier
     *
     * @param int $id
     */
    public function setIdentifier($id)
    {
        $this->identifier = $id;
    }

    /**
     * Getter for identifier
     *
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for isAction
     *
     * @param boolean $isAction
     */
    public function setIsAction($isAction)
    {
        $this->isAction = $isAction;
    }

    /**
     * Getter for isAction
     *
     * @return boolean
     */
    public function isAction()
    {
        return $this->isAction;
    }

    /**
     * Setter for actionId
     *
     * @param int $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * Getter for actionId
     *
     * @return int
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Setter for actionName
     *
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Setter for request
     *
     * @param \Zend\Http\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Getter for request
     *
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Get action service
     *
     * @return string
     */
    public function getActionService()
    {
        return $this->actionService;
    }

    /**
     * Return the action data bundle
     *
     * @return array
     */
    public function getActionDataBundle()
    {
        return $this->actionDataBundle;
    }

    /**
     * Return the action data map
     *
     * @return array
     */
    public function getActionDataMap()
    {
        return $this->actionDataMap;
    }

    /**
     * Get data map
     *
     * @return array
     */
    public function getDataMap()
    {
        return $this->dataMap;
    }

    /**
     * Return the form tables
     *
     * @return array
     */
    public function getFormTables()
    {
        return $this->formTables;
    }

    /**
     * Return the data bundle
     *
     * @return array
     */
    public function getDataBundle()
    {
        return $this->dataBundle;
    }

    /**
     * Get table data bundle
     *
     * @return array
     */
    public function getTableDataBundle()
    {
        return $this->tableDataBundle;
    }

    /**
     *
     * @param \Common\Controller\Service\SectionServiceFactory $factory
     */
    public function setSectionServiceFactory(SectionServiceFactory $factory)
    {
        $this->sectionServiceFactory = $factory;
    }

    /**
     * Get another section service
     *
     * @param string $name
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getSectionService($name)
    {
        if (!isset($this->sectionServices[$name])) {
            $this->sectionServices[$name] = $this->sectionServiceFactory->getSectionService($name);
            $this->configureSectionService($this->sectionServices[$name]);
        }

        return $this->sectionServices[$name];
    }

    /**
     * Create a brand new instance of section service
     *
     * @param string $name
     */
    protected function createSectionService($name)
    {
        $service = $this->sectionServiceFactory->createSectionService($name);
        $this->configureSectionService($service);

        return $service;
    }

    /**
     * Configure a section service
     *
     * @param \Common\Controller\Service\SectionServiceInterface $service
     */
    protected function configureSectionService($service)
    {
        $service->setIdentifier($this->getIdentifier());
        $service->setIsAction($this->isAction());
        $service->setActionId($this->getActionId());
        $service->setActionName($this->getActionName());
        $service->setRequest($this->getRequest());
    }

    /**
     * Lock the element
     *
     * @param \Zend\Form\Element $element
     * @param string $message
     */
    public function lockElement(Element $element, $message)
    {
        $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');

        $lockView = new ViewModel(array('message' => $this->translate($message)));
        $lockView->setTemplate('partials/lock');

        $element->setLabel($element->getLabel() . $viewRenderer->render($lockView));
        $element->setLabelOption('disable_html_escape', true);

        $attributes = $element->getLabelAttributes();

        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }
        // @todo add this back in when the css has been tweaked
        //$attributes['class'] .= ' tooltip-grandparent';

        $element->setLabelAttributes($attributes);
    }

    /**
     * Remove a list of form fields
     *
     * @param \Zend\Form\Form $form
     * @param string $fieldset
     * @param array $fields
     */
    public function removeFormFields(Form $form, $fieldset, array $fields)
    {
        foreach ($fields as $field) {
            $form->get($fieldset)->remove($field);
        }
    }

    /**
     * Load sub section data
     *
     * @param int $id
     * @return array
     */
    public function actionLoad($id)
    {
        if ($this->actionData === null) {

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
    public function actionSave($data, $service = null)
    {
        if ($service === null) {
            $service = $this->getActionService();
        }

        $method = 'POST';

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        }

        return $this->makeRestCall($service, $method, $data);
    }

    /**
     * Save the data
     *
     * @param array $data
     */
    public function save($data, $service = null)
    {
        if ($service === null) {
            $service = $this->getService();
        }

        $method = 'POST';

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        }

        return $this->makeRestCall($service, $method, $data);
    }

    /**
     * Default method to get form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    public function getFormTableData($id, $table)
    {
        return array();
    }

    /**
     * Load the current record
     *
     * @return array
     */
    public function loadCurrent()
    {
        return $this->load($this->getIdentifier());
    }

    /**
     * Remove trailer elements for PSV and set up Traffic Area section
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        return $form;
    }

    /**
     * Alter the action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterActionForm(Form $form)
    {
        return $form;
    }

    /**
     * Alter the delete action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterDeleteForm(Form $form)
    {
        return $form;
    }

    /**
     * Default Process delete
     *
     * @param array $data
     */
    public function deleteSave($data)
    {
        $ids = explode(',', $data['data']['id']);

        foreach ($ids as $id) {
            $this->delete($id, $this->getActionService());
        }
    }

    /**
     * Delete
     *
     * @return Response
     */
    public function delete($id = null, $service = null)
    {
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
     * Default Load data for the delete confirmation form
     *
     * @param int $id
     * @return array
     */
    public function deleteLoad($id)
    {
        if (is_array($id)) {
            $id = implode(',', $id);
        }

        return array('data' => array('id' => $id));
    }

    /**
     * Alter table
     *
     * @param \Common\Service\Table\TableBuilder $table
     * @return \Common\Service\Table\TableBuilder
     */
    public function alterTable($table)
    {
        return $table;
    }

    /**
     * Load data for the form
     *
     * @param int $id
     * @return array
     */
    public function load($id)
    {
        if (empty($this->loadedData)) {
            $service = $this->getService();

            $result = $this->makeRestCall($service, 'GET', $id, $this->getDataBundle());

            $this->loadedData = $result;
        }

        return $this->loadedData;
    }

    /**
     * Save crud data
     *
     * @param array $data
     * @return mixed
     */
    public function saveCrud($data)
    {
        return $this->save($data);
    }

    /**
     * Map the data on load
     *
     * @param array $data
     * @return array
     */
    public function processLoad($data)
    {
        return $data;
    }

    /**
     * Process loading the sub section data
     *
     * @param array $data
     * @return array
     */
    public function processActionLoad($data)
    {
        return $data;
    }

    /**
     * Process file uploads
     *
     * @param array $uploads
     * @param Form $form
     * @return array
     */
    public function processFileUploads($uploads, $form)
    {
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

                    // @todo need to sort this out
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

    /**
     * set the field value for a given key. This allows us
     * to override form data which has been previously set
     *
     * @param string $key
     * @param mixed $value
     */
    public function setFieldValue($key, $value)
    {
        $this->fieldValues[$key] = $value;
    }

    /**
     * Getter for field values
     *
     * @return array
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * Called after form->setData
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function postSetFormData(Form $form)
    {
        return $form;
    }

    /**
     * Remove file
     *
     * @param int $id
     */
    public function deleteFile($id, $fieldset, $name)
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
     * Get uploader
     *
     * @return object
     */
    public function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader')->getUploader();
    }

    /**
     * Set service
     *
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Set action service
     *
     * @param string $actionService
     */
    public function setActionService($actionService)
    {
        $this->actionService = $actionService;
    }

    /**
     * Set action data bundle
     *
     * @param array $actionDataBundle
     */
    public function setActionDataBundle($actionDataBundle)
    {
        $this->actionDataBundle = $actionDataBundle;
    }
}
