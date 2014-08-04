<?php

/**
 * An abstract form controller that all ordinary OLCS controllers inherit from
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Controller;

use Common\Form\Elements\Types\Address;
use Common\Form\Elements\Types\Person;
use Zend\Mvc\MvcEvent;

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

    private $persist = true;

    private $fieldValues = array();

    /**
     * @codeCoverageIgnore
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $onDispatch = parent::onDispatch($e);

        // This must stay here due to a race condition.
        if ($this instanceof CrudInterface) {
            $this->checkForCancelButton('cancel');
        }

        return $onDispatch;
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

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    protected function getForm($type)
    {
        $form = $this->getServiceLocator()->get('OlcsCustomForm')->createForm($type);

        $form = $this->processPostcodeLookup($form);

        $form = $this->processEntityLookup($form);

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

            if (!$this->validateForm || ($this->persist && $form->isValid())) {

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

                $params = array_merge($params, $this->getCallbackData());

                $this->callCallbackIfExists($callback, $params);
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
     * Method to trigger generation of a document providing a generate checkox
     * is found in $data
     *
     * @param arrat $data
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

        $data[$addressName]['country'] = str_replace('country.', '', $data[$addressName]['country']);

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
     * Process the person lookup functionality
     *
     * @param Form $form
     * @return Form
     */
    protected function processEntityLookup($form)
    {

        $fieldsets = $form->getFieldsets();

        foreach ($fieldsets as $fieldset) {
            if ($fieldset instanceof Person) {
                $searchFieldset = $this->processPersonLookup($fieldset);
                if ($searchFieldset instanceof \Common\Form\Elements\Types\PersonSearch) {
                    $elements = $searchFieldset->getElements();
                    foreach($elements as $element) {
                        $fieldset->add($element);
                    }
                }
            }
        }

        return $form;
    }

    /**
     * Method to manipulate the form as neccessary
     *
     * @param type $fieldset
     * @param type $post
     * @return type
     */
    protected function processPersonLookup($fieldset)
    {
        $this->setPersist(false);
        $request = $this->getRequest();

        $name = $fieldset->getName();

        $post = array();
        if ($request->isPost()) {
            $post = (array)$request->getPost();
        }
        $searchFieldset = false;
        // If we haven't posted a form, or we haven't clicked find person
        if (isset($post[$name]['lookupTypeSubmit'])
            && !empty($post[$name]['lookupTypeSubmit'])) {
            // get the relevant search form
            $searchFieldset = $this->processPersonType($fieldset, $post);

        } elseif (isset($post[$name]['search'])
            && !empty($post[$name]['search'])) {
            // get the relevant results
            $searchFieldset = $this->processPersonSearch($fieldset, $post);

        } elseif (isset($post[$name]['select'])
            && !empty($post[$name]['select'])) {
            // get the relevant entity and populate the relevant fields
            $searchFieldset = $this->processPersonSelected($fieldset, $post);

        } else {
            // add the search fieldset to ensure the relevant person/operator
            // form elements are present based on defType
            $searchFieldset = new \Common\Form\Elements\Types\PersonSearch('searchPerson', array('label' => 'Select'));
            $searchFieldset->setAttributes(
                array(
                    'type' => 'person-search',
                )
            );
            $searchFieldset->setLabel('Search for person');
            $searchFieldset->remove('person-list');
            $searchFieldset->remove('select');

            $this->setPersist(true);
        }

        return $searchFieldset;
    }

    protected function processPersonType($fieldset, $post)
    {
        $this->setPersist(false);

        $search = new \Common\Form\Elements\Types\PersonSearch('searchPerson', array('label' => 'Select'));
        $search->setAttributes(
            array(
                'type' => 'person-search',
            )
        );

        $search->setLabel('Search for person');

        $search->remove('person-list');
        $search->remove('select');
        $search->remove('personFirstname');
        $search->remove('personLastname');
        $search->remove('dateOfBirth');

        return $search;
    }

    protected function processPersonSearch($fieldset, $post)
    {
        $this->setPersist(false);

        $search = new \Common\Form\Elements\Types\PersonSearch('searchPerson', array('label' => 'Select'));
        $search->setAttributes(
            array(
                'type' => 'person-search',
            )
        );

        $personName = trim($post[$fieldset->getName()]['personSearch']);

        if (empty($personName)) {
            $search->setMessages(
                array('Please enter a person name')
            );
        } else {

            $personList = $this->getPersonListForName($personName);

            if (empty($personList)) {

                $search->setMessages(
                    array('No person found for name')
                );

            } else {
                $search->get('person-list')->setValueOptions(
                    $this->formatPersonsForSelect($personList)
                );

            }

        }

        $search->setLabel('Search for person');
        $search->remove('personFirstname');
        $search->remove('personLastname');
        $search->remove('dateOfBirth');
        return $search;
    }

    protected function processPersonSelected($fieldset, $post)
    {
        $this->setPersist(false);

        $search = new \Common\Form\Elements\Types\PersonSearch('searchPerson', array('label' => 'Select'));
        $search->setAttributes(
            array(
                'type' => 'person-search',
            )
        );
        $search->setLabel('Search for person');
        $search->remove('person-list');
        $search->remove('select');

        $person = $this->getPersonById($post[$fieldset->getName()]['person-list']);

        $this->fieldValues[$fieldset->getName()] = array_merge($post[$fieldset->getName()], $person);

        return $search;
    }


    /**
     * Method to retrieve the results of a search by name
     *
     * @param string $name
     * @return array
     */
    protected function getPersonListForName($name)
    {
        $data['name'] = $name;
        $results = $this->makeRestCall('DefendantSearch', 'GET', $data);

        return $results['Results'];
    }

    /**
     * Method to format the person list result into format suitable for select
     * dropdown
     *
     * @param array $person_list
     * @return array
     */
    private function formatPersonsForSelect($person_list)
    {
        $result = [];
        if (is_array($person_list)) {
            foreach ($person_list as $person)
            {
                $dob = new \DateTime($person['date_of_birth']);
                $result[$person['id']] = trim($person['surname'] .
                    ',  ' . $person['first_name'] .
                    '     (b. ' . $dob->format('d-M-Y')) . ')';
            }
        }

        return $result;
    }

    /**
     * Method to format a person details from db result into form field array
     * structure
     *
     * @param type $person_details
     * @return type
     * @todo get date of birth to prepopulate form
     */
    private function formatPerson($person_details)
    {
        $result['personFirstname'] = $person_details['firstName'];
        $result['personLastname'] = $person_details['surname'];

        $result['dateOfBirth'] = $person_details['dateOfBirth'];

        return $result;
    }

    /**
     * Method to perform a final look up on the person selected.
     *
     * @param type $id
     * @return type
     * @todo Call relevent backend service to get person details
     */
    private function getPersonById($id)
    {
        $result = $this->makeRestCall('Person', 'GET', ['id' => $id]);

        if ($result) {
            return $this->formatPerson($result);
        }
        return [];
    }
}
