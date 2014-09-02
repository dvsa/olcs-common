<?php

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Controller;

use Common\Form\Elements\Types\Address;
use Zend\Filter\Word\DashToCamelCase;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Factory;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

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

    protected $persist = true;

    private $fieldValues = array();

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
        $this->persist = $persist;
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
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $annotationBuilder = new AnnotationBuilder();
        $annotationBuilder->setFormFactory(new Factory($formElementManager));
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
        return $this->sendGet('postcode\address', array('id' => $uprn), true);
    }

    protected function getAddressesForPostcode($postcode)
    {
        return $this->sendGet('postcode\address', array('postcode' => $postcode), true);
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
            if (!$this->validateForm || ($this->persist && $form->isValid())) {

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
                    new \Zend\Validator\ValidatorChain()
                );
            }
        }

        foreach ($form->getFieldsets() as $key => $fieldset) {

            foreach ($fieldset->getElements() as $elementKey => $element) {

                $value = $element->getValue();

                if (empty($value)) {

                    $form->getInputFilter()->get($key)->get($elementKey)->setAllowEmpty(true);
                    $form->getInputFilter()->get($key)->get($elementKey)->setValidatorChain(
                        new \Zend\Validator\ValidatorChain()
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
     * Process file uploads
     *
     * @param array $uploads
     * @param Form $form
     * @return array
     */
    protected function processFileUploads($uploads, $form)
    {
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $files = $this->getRequest()->getFiles()->toArray();

            return $this->processFileUpload($uploads, $post, $files, $form);
        }

        return array();
    }

    /**
     * Process file deletions
     *
     * @param array $uploads
     * @param Form $form
     * @return array
     */
    protected function processFileDeletions($uploads, $form)
    {
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            return $this->processFileDeletion($uploads, $post, $form);
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
    protected function processFileUpload($uploads, $data, $files, $form)
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

                } elseif (
                    isset($data[$fieldset]['file-controls']['upload'])
                    && !empty($data[$fieldset]['file-controls']['upload'])
                ) {

                    $this->setPersist(false);

                    $error = $files[$fieldset]['file-controls']['file']['error'];

                    $validator = $this->getFileSizeValidator();

                    if (
                        $error == UPLOAD_ERR_OK
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
     * Process a single file deletion
     *
     * @param array $uploads
     * @param array $data
     * @param Form $form
     * @return array
     */
    protected function processFileDeletion($uploads, $data, $form)
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

    public function getFileSizeValidator()
    {
        return new \Zend\Validator\File\FilesSize('2MB');
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
     * Gets the licence by ID.
     *
     * @param integer $id
     * @return array
     */
    public function getLicence($id)
    {
        $licence = $this->makeRestCall('Licence', 'GET', array('id' => $id));

        return $licence;
    }
}
