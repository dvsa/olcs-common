<?php

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Form\Elements\Types\Address;
use Common\Service\Table\TableBuilder;
use Zend\Form\Element;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\DateSelect;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Http\Request;
use Zend\I18n\Validator\PostCode as PostcodeValidator;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\ValidatorChain;
use Zend\View\Model\ViewModel;

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormHelperService extends AbstractHelperService
{
    const ALTER_LABEL_RESET = 0;
    const ALTER_LABEL_APPEND = 1;
    const ALTER_LABEL_PREPEND = 2;

    const CSRF_TIMEOUT = 3600;

    const MIN_COMPANY_NUMBER_LENGTH = 1;
    const MAX_COMPANY_NUMBER_LENGTH = 8;

    /**
     * Create a form
     *
     * @param string $formName
     * @param bool $addCsrf
     * @param bool $addContinue
     *
     * @return \Zend\Form\Form
     * @throws \Exception
     */
    public function createForm($formName, $addCsrf = true, $addContinue = true)
    {
        if (class_exists($formName)) {
            $class = $formName;
        } else {
            $class = $this->findForm($formName);
        }

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');

        /** @var \Zend\Form\FormInterface $form */
        $form = $annotationBuilder->createForm($class);

        if ($addCsrf) {
            $config = array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'security',
                'attributes' => array(
                    'class' => 'js-csrf-token',
                ),
                'options' => array(
                    'csrf_options' => array(
                        'messageTemplates' => array(
                            'notSame' => 'csrf-message'
                        ),
                        'timeout' => self::CSRF_TIMEOUT
                    )
                )
            );
            $form->add($config);
        }

        if ($addContinue) {
            $config = array(
                'type' => '\Zend\Form\Element\Button',
                'name' => 'form-actions[continue]',
                'options' => array(
                    'label' => 'Continue'
                ),
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'visually-hidden',
                    'id' => 'hidden-continue'
                )
            );
            $form->add($config);
        }

        $authService = $this->getServiceLocator()->get(\ZfcRbac\Service\AuthorizationService::class);

        if ($authService->isGranted('internal-user')) {
            if (!$authService->isGranted('internal-edit') && !$form->getOption('bypass_auth')) {
                $form->setOption('readonly', true);
            }
        }

        return $form;
    }

    /**
     * @param \Zend\Form\Form $form
     * @param \Zend\Http\Request $request
     */
    public function setFormActionFromRequest($form, $request)
    {
        if (!$form->hasAttribute('action')) {
            $url = $request->getUri()->getPath();
            $query = $request->getUri()->getQuery();

            if ($query !== '') {
                $url .= '?' . $query;
            } elseif (substr($url, -1) === '/') {
                // @NOTE Had to add the following check in, as the trailing space hack was breaking filter forms
                if (strtoupper($form->getAttribute('method')) === 'GET') {
                    $url .= '?i=e';
                } else {
                    // WARNING: As rubbish as this looks, do *not* remove
                    // the trailing space. When rendering forms in modals,
                    // IE strips quote marks off attributes wherever possible.
                    // This means that an action of /foo/bar/baz/ will render
                    // without quotes, and the trailing slash will self-close
                    // and completely destroy the form
                    $url .= ' ';
                }
            }

            $form->setAttribute('action', $url);
        }
    }

    public function createFormWithRequest($formName, $request)
    {
        $form = $this->createForm($formName);

        $this->setFormActionFromRequest($form, $request);

        return $form;
    }

    /**
     * Find form
     *
     * @param string $formName
     * @return string
     * @throws \Exception
     */
    private function findForm($formName)
    {
        foreach (['Olcs', 'Common', 'Admin'] as $namespace) {
            $class = $namespace . '\Form\Model\Form\\' . $formName;

            if (class_exists($class)) {
                return $class;
            }
        }

        throw new \RuntimeException('Form does not exist: ' . $formName);
    }

    /**
     * Check for address lookups
     *  Returns true if an address search is present, false otherwise
     *
     * @param Form $form
     * @param Request $request
     * @return boolean
     */
    public function processAddressLookupForm(Form $form, Request $request)
    {
        $processed = false;
        $modified  = false;
        $fieldsets = $form->getFieldsets();
        $post      = (array)$request->getPost();

        foreach ($fieldsets as $fieldset) {
            if ($result = $this->processAddressLookupFieldset($fieldset, $post, $form)) {
                // @NOTE we can't just return true here, as any other address lookups need processing also
                $processed = true;

                if (is_array($result)) {
                    $modified = true;
                    $post = $result;
                }
            }
        }

        /**
         * A postcode -> address lookup will have modified the array of
         * POST data at an unknown level of nesting, so we need to make
         * one top-level call to re-populate the form data if so
         */
        if ($modified) {
            $form->setData($post);
        }

        return $processed;
    }

    /**
     * Process an address lookup fieldset
     *
     * @param Fieldset $fieldset
     * @param array $post
     * @return boolean
     */
    private function processAddressLookupFieldset($fieldset, $post, $form)
    {
        $name = $fieldset->getName();

        if (!($fieldset instanceof Address)) {
            $data = isset($post[$name]) ? $post[$name] : [];
            $processed = false;
            $modified  = false;

            //  #TODO possible bug :: Variable $fieldset is introduced as a method parameter and overridden here
            foreach ($fieldset->getFieldsets() as $fieldset) {
                if ($result = $this->processAddressLookupFieldset($fieldset, $data, $form)) {
                    $processed = true;

                    if (is_array($result)) {
                        $modified = true;
                        $post[$name] = $result;
                    }
                }
            }
            if ($modified) {
                return $post;
            }
            return $processed;
        }

        // If we have clicked the find address button
        if (isset($post[$name]['searchPostcode']['search']) && !empty($post[$name]['searchPostcode']['search'])) {

            $this->processPostcodeSearch($fieldset, $post, $name);
            return true;
        }

        // If we have selected an address
        if (isset($post[$name]['searchPostcode']['select']) && !empty($post[$name]['searchPostcode']['select'])) {

            $this->removeAddressSelectFields($fieldset);

            // manipulate the current level of post data, bearing in mind
            // we could be nested at this point...
            $post[$name] = $this->processAddressSelect($post, $name);

            // ... meaning we have to return the current level of post data so
            // it can bubble all the way back up to the top
            return $post;
        }

        $this->removeAddressSelectFields($fieldset);
        return false;
    }

    /**
     * Process postcode lookup
     *
     * @param \Zend\Form\Fieldset $fieldset
     * @param array $post
     * @param string $name
     * @return boolean
     */
    private function processPostcodeSearch($fieldset, $post, $name)
    {
        $postcode = trim($post[$name]['searchPostcode']['postcode']);

        // If we haven't entered a postcode
        if (empty($postcode)) {

            $this->removeAddressSelectFields($fieldset);

            $fieldset->get('searchPostcode')->setMessages(array('Please enter a postcode'));

            return false;
        }

        try {
            $addressList = $this->getServiceLocator()->get('Data\Address')->getAddressesForPostcode($postcode);
        } catch (\Exception $e) {
            // RestClient / ResponseHelper throw root exceptions :(
            $fieldset->get('searchPostcode')->setMessages(array('postcode.error.not-available'));
            $this->removeAddressSelectFields($fieldset);
            return false;
        }

        // If we haven't found any addresses
        if (empty($addressList)) {

            $this->removeAddressSelectFields($fieldset);

            $fieldset->get('searchPostcode')->setMessages(array('postcode.error.no-addresses-found'));

            return false;
        }

        $fieldset->get('searchPostcode')->get('addresses')->setValueOptions(
            $this->getServiceLocator()->get('Helper\Address')->formatAddressesForSelect($addressList)
        );

        return true;
    }

    /**
     * Process address select
     *
     * @param \Zend\Form\Fieldset $fieldset
     * @param array $post
     * @param string $name
     */
    private function processAddressSelect($post, $name)
    {
        $address = $this->getServiceLocator()->get('Data\Address')
            ->getAddressForUprn($post[$name]['searchPostcode']['addresses']);

        return $this->getServiceLocator()->get('Helper\Address')->formatPostalAddress($address);
    }

    /**
     * Remove address select fields
     *
     * @param \Zend\Form\Fieldset $fieldset
     */
    private function removeAddressSelectFields($fieldset)
    {
        $fieldset->get('searchPostcode')->remove('addresses');
        $fieldset->get('searchPostcode')->remove('select');
    }

    /**
     * Alter an elements label
     *
     * @param \Zend\Form\Element $element
     * @param string $label
     * @param int $type
     */
    public function alterElementLabel($element, $label, $type = self::ALTER_LABEL_RESET)
    {
        if (in_array($type, array(self::ALTER_LABEL_APPEND, self::ALTER_LABEL_PREPEND), false)) {
            $oldLabel = $element->getLabel();

            if ($type == self::ALTER_LABEL_APPEND) {
                $label = $oldLabel . $label;
            } else {
                $label = $label . $oldLabel;
            }
        }

        $element->setLabel($label);
    }

    /**
     * When passed something like
     * $form, 'data->registeredAddress', this method will remove the element from the form and input filter
     *
     * @param \Zend\Form\Form $form
     * @param string $elementReference
     */
    public function remove($form, $elementReference)
    {
        $filter = $form->getInputFilter();

        $this->removeElement($form, $filter, $elementReference);

        return $this;
    }

    private function removeElement($form, InputFilterInterface $filter, $elementReference)
    {
        list($form, $filter, $name) = $this->getElementAndInputParents($form, $filter, $elementReference);

        $form->remove($name);
        $filter->remove($name);
    }

    /**
     * Grab the parent input filter and fieldset from the top level form and input filter using the -> notation
     * i.e. data->field would return the data fieldset, data input filter and the string field
     */
    public function getElementAndInputParents($form, InputFilterInterface $filter, $elementReference)
    {
        if (false !== strpos($elementReference, '->')) {
            list($container, $elementReference) = explode('->', $elementReference, 2);

            return $this->getElementAndInputParents(
                $form->get($container),
                $filter->get($container),
                $elementReference
            );
        }

        return array($form, $filter, $elementReference);
    }

    /**
     * Disable empty validation
     *
     * @param Fieldset $form
     * @param InputFilter $filter
     */
    public function disableEmptyValidation(Fieldset $form, InputFilter $filter = null)
    {
        if ($filter === null) {
            $filter = $form->getInputFilter();
        }

        foreach ($form->getElements() as $key => $element) {

            $value = $element->getValue();

            if (empty($value) || $element instanceof Checkbox) {

                $filter->get($key)->setAllowEmpty(true)
                    ->setRequired(false)
                    ->setValidatorChain(
                        new ValidatorChain()
                    );
            }
        }

        if ($form instanceof Fieldset) {
            foreach ($form->getFieldsets() as $fieldset) {
                if ($filter->has($fieldset->getName())) {
                    $this->disableEmptyValidation($fieldset, $filter->get($fieldset->getName()));
                }
            }
        }
    }

    /**
     * Disable empty validation on a single element
     *
     * @param \Zend\Form\Form $form
     * @param string $reference
     * @return null
     */
    public function disableEmptyValidationOnElement($form, $reference)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $name) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);
        $filter->get($name)->setAllowEmpty(true);
        $filter->get($name)->setRequired(false);
    }

    /**
     * Populate form table
     *
     * @param \Zend\Form\Fieldset $fieldset
     * @param \Common\Service\Table\TableBuilder $table
     */
    public function populateFormTable(Fieldset $fieldset, TableBuilder $table, $tableFieldsetName = null)
    {
        $fieldset->get('table')->setTable($table, $tableFieldsetName);
        $fieldset->get('rows')->setValue(count($table->getRows()));
    }

    /**
     * Recurse through the form and the input filter to disable the final result
     *
     * @param \Zend\Form\Form $form
     * @param string $reference
     * @param \Zend\InputFilter\InputFilter $filter
     * @return null
     */
    public function disableElement($form, $reference, $filter = null)
    {
        if ($filter === null) {
            $filter = $form->getInputFilter();
        }

        if (false !== strpos($reference, '->')) {
            list($index, $reference) = explode('->', $reference, 2);

            return $this->disableElement($form->get($index), $reference, $filter->get($index));
        }

        $element = $form->get($reference);

        if ($element instanceof DateSelect) {
            $this->disableDateElement($element);
        } else {
            $element->setAttribute('disabled', 'disabled');
        }

        $filter->get($reference)->setAllowEmpty(true);
        $filter->get($reference)->setRequired(false);

        return null;
    }

    /**
     * Disable date element
     *
     * @param \Zend\Form\Element\DateSelect $element
     */
    public function disableDateElement($element)
    {
        $element->getDayElement()->setAttribute('disabled', 'disabled');
        $element->getMonthElement()->setAttribute('disabled', 'disabled');
        $element->getYearElement()->setAttribute('disabled', 'disabled');
    }

    /**
     * Enable date element
     *
     * @param \Zend\Form\Element\DateSelect $element
     */
    public function enableDateElement($element)
    {
        $element->getDayElement()->removeAttribute('disabled');
        $element->getMonthElement()->removeAttribute('disabled');
        $element->getYearElement()->removeAttribute('disabled');
    }

    /**
     * Disable all elements recursively
     *
     * @param \Zend\Form\Fieldset $elements
     * @return null
     */
    public function disableElements($elements)
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

        if ($elements instanceof DateSelect) {
            $this->disableDateElement($elements);
            return;
        }

        if ($elements instanceof Element) {
            $elements->setAttribute('disabled', 'disabled');
        }
    }

    /**
     * Enable all elements recursively
     *
     * @param \Zend\Form\Fieldset $elements
     * @return null
     */
    public function enableElements($elements)
    {
        if ($elements instanceof Fieldset) {
            foreach ($elements->getElements() as $element) {
                $this->enableElements($element);
            }

            foreach ($elements->getFieldsets() as $fieldset) {
                $this->enableElements($fieldset);
            }
            return;
        }

        if ($elements instanceof DateSelect) {
            $this->enableDateElement($elements);
            return;
        }

        if ($elements instanceof Element) {
            $elements->removeAttribute('disabled');
        }
    }

    /**
     * Disable field validation
     *
     * @param \Zend\InputFilter\InputFilter $inputFilter
     * @return null
     */
    public function disableValidation($inputFilter)
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
     * Lock the element
     *
     * @param \Zend\Form\Element $element
     * @param string $message
     */
    public function lockElement(Element $element, $message)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');

        $lockView = new ViewModel(
            array('message' => $translator->translate($message))
        );
        $lockView->setTemplate('partials/lock');

        $label = $translator->translate($element->getLabel());

        $element->setLabel($label . $viewRenderer->render($lockView));
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
    public function removeFieldList(Form $form, $fieldset, array $fields)
    {
        foreach ($fields as $field) {
            $this->remove($form, $fieldset . '->' . $field);
        }
    }

    /**
     * Check for company number lookups
     *
     * @NOTE Doesn't quite adhere to the same interface as the other process*LookupForm
     * methods as it already expects the presence of a company number field to have been
     * determined, and it expects an array of data rather than a request
     *
     * @param Form $form
     * @param array $data
     * @param string $detailsFieldset
     * @param string $addressFieldset
     * @return boolean
     */
    public function processCompanyNumberLookupForm(Form $form, $data, $detailsFieldset, $addressFieldset = null)
    {
        $companyNumber = $data[$detailsFieldset]['companyNumber']['company_number'];
        if (strlen($companyNumber) >= self::MIN_COMPANY_NUMBER_LENGTH &&
            strlen($companyNumber) <= self::MAX_COMPANY_NUMBER_LENGTH) {

            list($result, $message) = $this->doCompanySearch($companyNumber);

            // company not found so let's try alternative search with/without leading zero
            if (!isset($message) && !$result['Count']) {
                if (substr($companyNumber, 0, 1) === '0' && strlen($companyNumber) == self::MAX_COMPANY_NUMBER_LENGTH) {
                    $companyNumber = ltrim($companyNumber, '0');
                } else {
                    $companyNumber = str_pad($companyNumber, self::MAX_COMPANY_NUMBER_LENGTH, "0", STR_PAD_LEFT);
                }
                if (strlen($companyNumber) >= self::MIN_COMPANY_NUMBER_LENGTH  &&
                    strlen($companyNumber) <= self::MAX_COMPANY_NUMBER_LENGTH) {
                    list($result, $message) = $this->doCompanySearch($companyNumber);
                }
            }

            if (isset($result) && $result['Count'] === 1) {

                $form->get($detailsFieldset)->get('name')->setValue($result['Results'][0]['CompanyName']);

                if ($addressFieldset && isset($result['Results'][0]['RegAddress']['AddressLine'])) {
                    $this->populateRegisteredAddressFieldset(
                        $form->get($addressFieldset),
                        $result['Results'][0]['RegAddress']['AddressLine']
                    );
                }

                return;
            }

            if (!isset($message)) {
                $message = 'company_number.search_no_results.error';
            }
        } else {
            $message = 'company_number.length.validation.error';
        }

        $translator = $this->getServiceLocator()->get('translator');

        $form->get($detailsFieldset)->get('companyNumber')->setMessages(
            array(
                'company_number' => array($translator->translate($message))
            )
        );
    }

    protected function doCompanySearch($companyNumber)
    {
        $result = null;
        $message = null;
        try {
            $result = $this->getServiceLocator()
                ->get('Data\CompaniesHouse')
                ->search('companyDetails', $companyNumber);
        } catch (\Exception $e) {
            // ResponseHelper throws root-level exceptions so can't be more specific here :(
            $message = 'company_number.search_error.error';
        }
        return [$result, $message];
    }

    /**
     * Remove a value option from an element
     *
     * @param Element $element Select element or a Radio group
     * @param string  $index
     */
    public function removeOption(Element $element, $index)
    {
        $options = $element->getValueOptions();

        if (isset($options[$index])) {
            unset($options[$index]);
            $element->setValueOptions($options);
        }
    }

    public function setCurrentOption(Element $element, $index)
    {
        $options = $element->getValueOptions();

        if (isset($options[$index])) {
            $translator = $this->getServiceLocator()->get('Helper\Translation');

            $options[$index] .= ' ' . $translator->translate('current.option.suffix');

            $element->setValueOptions($options);
        }
    }

    public function removeValidator(FormInterface $form, $reference, $validatorClass)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $field) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $filter->get($field)->getValidatorChain();
        $newValidatorChain = new ValidatorChain();

        foreach ($validatorChain->getValidators() as $validator) {
            if (! ($validator['instance'] instanceof $validatorClass)) {
                $newValidatorChain->attach($validator['instance']);
            }
        }

        $filter->get($field)->setValidatorChain($newValidatorChain);
    }

    public function attachValidator(FormInterface $form, $reference, $validator)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $field) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $filter->get($field)->getValidatorChain();

        $validatorChain->attach($validator);
    }

    public function getValidator(FormInterface $form, $reference, $validatorClass)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $field) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $filter->get($field)->getValidatorChain();

        foreach ($validatorChain->getValidators() as $validator) {
            if ($validator['instance'] instanceof $validatorClass) {
                return $validator['instance'];
            }
        }

        return null;
    }

    /**
     * Set appropriate default values on date fields
     *
     * @param \Zend\Form\Element $field
     * @param \DateTime $currentDate
     * @return \Zend\Form\Element
     */
    public function setDefaultDate($field)
    {
        // default to the current date if it is not set
        $currentValue = $field->getValue();
        $currentValue = trim($currentValue, '-'); // date element returns '--' when empty!
        if (empty($currentValue)) {
            $today = $this->getServiceLocator()->get('Helper\Date')->getDateObject();
            $field->setValue($today);
        }

        return $field;
    }

    /**
     * Populate an address fieldset using Companies House address data
     *
     * @param \Zend\Form\Fieldset $fieldset address fieldset
     * @param array $data Companies House 'AddressLine' data
     * @return \Zend\Form\Fieldset
     */
    public function populateRegisteredAddressFieldset($fieldset, $data)
    {
        // parse out postcode from address data
        $postcode = '';
        $postcodeValidator = new PostcodeValidator(['locale' => 'en-GB']);
        foreach ($data as $key => $datum) {
            if ($postcodeValidator->isValid($datum)) {
                $postcode =  $datum;
                unset($data[$key]);
            }
        }

        // populate remaining fields in order
        $fields = ['addressLine1', 'addressLine2', 'addressLine3', 'addressLine4', 'town'];
        $data = array_pad($data, count($fields), '');
        $addressData = array_combine($fields, $data);

        $addressData['postcode'] = $postcode;

        foreach ($addressData as $field => $value) {
            $fieldset->get($field)->setValue($value);
        }

        return $fieldset;
    }

    /**
     * Save form state data
     *
     * @param Form  $form
     * @param array $data The form data to save
     */
    public function saveFormState(Form $form, $data)
    {
        $sessionContainer = new \Zend\Session\Container('form_state');
        $sessionContainer->offsetSet($form->getName(), $data);
    }

    /**
     * Restore form state
     *
     * @param Form $form
     */
    public function restoreFormState(Form $form)
    {
        $sessionContainer = new \Zend\Session\Container('form_state');
        if ($sessionContainer->offsetExists($form->getName())) {
            $form->setData($sessionContainer->offsetGet($form->getName()));
        }
    }

    public function removeValueOption(Element $element, $key)
    {
        $options = $element->getValueOptions();

        unset($options[$key]);

        $element->setValueOptions($options);
    }
}
