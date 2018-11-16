<?php

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
use Zend\Validator\ValidatorInterface;

/**
 * @internal All validations to do with empty fields must be done as a validator
 *           within the fieldsets.  AllowEmpty is deprecated, so we use the NotEmpty
 *           validator.  All instances of AllowEmpty have been removed here for this
 *           primary reason.
 *
 * Form Helper Service
 */
class FormHelperService extends AbstractHelperService
{
    const ALTER_LABEL_RESET = 0;
    const ALTER_LABEL_APPEND = 1;
    const ALTER_LABEL_PREPEND = 2;

    const MIN_COMPANY_NUMBER_LENGTH = 1;
    const MAX_COMPANY_NUMBER_LENGTH = 8;

    /**
     * Create a form
     *
     * @param string $formName    Form Name
     * @param bool   $addCsrf     Is need add CSRF field
     * @param bool   $addContinue Is need add Continue button
     *
     * @return \Common\Form\Form
     */
    public function createForm($formName, $addCsrf = true, $addContinue = true)
    {
        if (class_exists($formName)) {
            $class = $formName;
        } else {
            $class = $this->findForm($formName);
        }

        $sm = $this->getServiceLocator();
        $annotationBuilder = $sm->get('FormAnnotationBuilder');
        $cfg = $sm->get('Config');

        /** @var \Common\Form\Form $form */
        $form = $annotationBuilder->createForm($class);

        //  add CSRF element
        if ($addCsrf) {
            $config = [
                'type' => \Zend\Form\Element\Csrf::class,
                'name' => 'security',
                'attributes' => [
                    'class' => 'js-csrf-token',
                ],
                'options' => [
                    'csrf_options' => [
                        'messageTemplates' => array(
                            'notSame' => 'csrf-message',
                        ),
                        'timeout' => $cfg['csrf']['timeout'],
                    ],
                ],
            ];
            $form->add($config);
        }

        //  add button "Continue" element
        if ($addContinue) {
            $config = array(
                'type' => \Zend\Form\Element\Button::class,
                'name' => 'form-actions[continue]',
                'options' => array(
                    'label' => 'Continue',
                ),
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'visually-hidden',
                    'style' => 'display: none;',
                    'id' => 'hidden-continue',
                ),
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
     * Set Form Action From Request
     *
     * @param \Zend\Form\FormInterface $form    Form
     * @param \Zend\Http\Request       $request Request
     *
     * @return void
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

    /**
     * Create Form With Request
     *
     * @param string             $formName Form name
     * @param \Zend\Http\Request $request  Request
     *
     * @return FormInterface
     */
    public function createFormWithRequest($formName, $request)
    {
        $form = $this->createForm($formName);

        $this->setFormActionFromRequest($form, $request);

        return $form;
    }

    /**
     * Find form
     *
     * @param string $formName Form Name
     *
     * @return string
     */
    private function findForm($formName)
    {
        foreach (['Olcs', 'Common', 'Admin', 'Permits'] as $namespace) {
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
     * @param \Zend\Form\FormInterface $form    Form
     * @param \Zend\Http\Request       $request Request
     *
     * @return boolean
     */
    public function processAddressLookupForm(Form $form, Request $request)
    {
        $processed = false;
        $modified = false;
        $fieldsets = $form->getFieldsets();
        $post = (array)$request->getPost();

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
     * @param \Zend\Form\Fieldset      $fieldset Fieldset
     * @param array                    $post     Post data
     * @param \Zend\Form\FormInterface $form     Form
     *
     * @return bool|array
     */
    private function processAddressLookupFieldset($fieldset, $post, $form)
    {
        $name = $fieldset->getName();

        if (!($fieldset instanceof Address)) {
            $data = isset($post[$name]) ? $post[$name] : [];
            $processed = false;
            $modified = false;

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
     * @param \Zend\Form\Fieldset $fieldset Fieldset
     * @param array               $post     Post data
     * @param string              $name     Field Name
     *
     * @return bool
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
     * @param array  $post Post data
     * @param string $name Name (unused)
     *
     * @return array
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
     * @param \Zend\Form\Fieldset $fieldset Fieldset
     *
     * @return void
     */
    private function removeAddressSelectFields($fieldset)
    {
        $fieldset->get('searchPostcode')->remove('addresses');
        $fieldset->get('searchPostcode')->remove('select');
    }

    /**
     * Alter an elements label
     *
     * @param \Zend\Form\Element $element Element
     * @param string             $label   Label text
     * @param int                $type    Alter type
     *
     * @return void
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
     * @param \Zend\Form\FormInterface $form             Form
     * @param string                   $elementReference Element ref
     *
     * @return $this
     */
    public function remove($form, $elementReference)
    {
        $filter = $form->getInputFilter();

        $this->removeElement($form, $filter, $elementReference);

        return $this;
    }

    /**
     * Remove element
     *
     * @param \Zend\Form\FormInterface $form             Form
     * @param InputFilterInterface     $filter           Filter
     * @param string                   $elementReference Element ref
     *
     * @return void
     */
    private function removeElement($form, InputFilterInterface $filter, $elementReference)
    {
        list($form, $filter, $name) = $this->getElementAndInputParents($form, $filter, $elementReference);

        $form->remove($name);
        $filter->remove($name);
    }

    /**
     * Grab the parent input filter and fieldset from the top level form and input filter using the -> notation
     * i.e. data->field would return the data fieldset, data input filter and the string field
     *
     * @param \Zend\Form\FormInterface $form             Form
     * @param InputFilterInterface     $filter           Filter
     * @param string                   $elementReference Element ref
     *
     * @return array
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
     * @param \Zend\Form\Fieldset|\Zend\Form\FormInterface $form   Form fieldset
     * @param InputFilter                                  $filter Filter
     *
     * @return void
     */
    public function disableEmptyValidation(Fieldset $form, InputFilter $filter = null)
    {
        if ($filter === null) {
            $filter = $form->getInputFilter();
        }

        /** @var \Zend\Form\ElementInterface $element */
        foreach ($form->getElements() as $key => $element) {
            $value = $element->getValue();

            if (empty($value) || $element instanceof Checkbox) {
                $filter->get($key)
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
     * @param \Zend\Form\FormInterface $form      Form
     * @param string                   $reference Element Ref
     *
     * @return void
     */
    public function disableEmptyValidationOnElement($form, $reference)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $name) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);
        $filter->get($name)->setRequired(false);
    }

    /**
     * Populate form table
     *
     * @param \Zend\Form\Fieldset                $fieldset          Fieldset
     * @param \Common\Service\Table\TableBuilder $table             Table
     * @param string|null                        $tableFieldsetName Fieldset name
     *
     * @return void
     */
    public function populateFormTable(Fieldset $fieldset, TableBuilder $table, $tableFieldsetName = null)
    {
        $fieldset->get('table')->setTable($table, $tableFieldsetName);
        $fieldset->get('rows')->setValue(count($table->getRows()));
    }

    /**
     * Recurse through the form and the input filter to disable the final result
     *
     * @param \Zend\Form\FormInterface      $form      Form
     * @param string                        $reference Ref
     * @param \Zend\InputFilter\InputFilter $filter    Filter
     *
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

        $filter->get($reference)->setRequired(false);

        return null;
    }

    /**
     * Disable date element
     *
     * @param \Zend\Form\Element\DateSelect $element Element
     *
     * @return void
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
     * @param \Zend\Form\Element\DateSelect $element Element
     *
     * @return void
     */
    public function enableDateElement($element)
    {
        $element->getDayElement()->removeAttribute('disabled');
        $element->getMonthElement()->removeAttribute('disabled');
        $element->getYearElement()->removeAttribute('disabled');
    }

    /**
     * Enable DateTime element
     *
     * @param \Zend\Form\Element\DateTimeSelect $element Element
     *
     * @return void
     */
    public function enableDateTimeElement($element)
    {
        $element->getDayElement()->removeAttribute('disabled');
        $element->getMonthElement()->removeAttribute('disabled');
        $element->getYearElement()->removeAttribute('disabled');
        $element->getHourElement()->removeAttribute('disabled');
        $element->getMinuteElement()->removeAttribute('disabled');
    }

    /**
     * Disable all elements recursively
     *
     * @param \Zend\Form\Fieldset $elements Elements
     *
     * @return void
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
     * @param \Zend\Form\Fieldset $elements Elements
     *
     * @return void
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
     * @param \Zend\InputFilter\InputFilter $inputFilter Input Filter
     *
     * @return void
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
            $inputFilter->setRequired(false);
            $inputFilter->setValidatorChain(new ValidatorChain());
        }
    }

    /**
     * Lock the element
     *
     * @param \Zend\Form\Element $element Element
     * @param string             $message Message
     *
     * @return void
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

        $element->setLabelAttributes($attributes);
    }

    /**
     * Remove a list of form fields
     *
     * @param \Zend\Form\FormInterface $form     Form
     * @param string                   $fieldset Name of Fieldset
     * @param array                    $fields   Names of Fields
     *
     * @return void
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
     * @param \Zend\Form\FormInterface $form            Form
     * @param array                    $data            Data
     * @param string                   $detailsFieldset Name of Details fieldset
     * @param string                   $addressFieldset Name of Address fieldset
     *
     * @NOTE Doesn't quite adhere to the same interface as the other process*LookupForm
     * methods as it already expects the presence of a company number field to have been
     * determined, and it expects an array of data rather than a request
     *
     * @return void
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
                if (strlen($companyNumber) >= self::MIN_COMPANY_NUMBER_LENGTH &&
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
                'company_number' => array($translator->translate($message)),
            )
        );
    }

    /**
     * Do Company Search
     *
     * @param string $companyNumber Company number
     *
     * @return array
     */
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
     * @param \Zend\Form\Element\(Select|Radio) $element Select element or a Radio group
     * @param string                            $index   Index
     *
     * @return void
     */
    public function removeOption(Element $element, $index)
    {
        $options = $element->getValueOptions();

        if (isset($options[$index])) {
            unset($options[$index]);
            $element->setValueOptions($options);
        }
    }

    /**
     * Set current option of element
     *
     * @param \Zend\Form\Element\(Select|Radio) $element Select element or a Radio group
     * @param string                            $index   Index
     *
     * @return void
     */
    public function setCurrentOption(Element $element, $index)
    {
        $options = $element->getValueOptions();

        if (isset($options[$index])) {
            $translator = $this->getServiceLocator()->get('Helper\Translation');

            $options[$index] = $translator->translate($options[$index]) . ' ' .
                $translator->translate('current.option.suffix');

            $element->setValueOptions($options);
        }
    }

    /**
     * Remove Validator
     *
     * @param \Zend\Form\FormInterface $form           Form
     * @param string                   $reference      Field Ref
     * @param string                   $validatorClass Validator Class
     *
     * @return void
     */
    public function removeValidator(FormInterface $form, $reference, $validatorClass)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $field) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $filter->get($field)->getValidatorChain();
        $newValidatorChain = new ValidatorChain();

        foreach ($validatorChain->getValidators() as $validator) {
            if (!($validator['instance'] instanceof $validatorClass)) {
                $newValidatorChain->attach($validator['instance']);
            }
        }

        $filter->get($field)->setValidatorChain($newValidatorChain);
    }

    /**
     * Attach Validator
     *
     * @param \Zend\Form\FormInterface           $form      Form
     * @param string                             $reference Field Ref
     * @param \Zend\Validator\ValidatorInterface $validator Validator Class
     *
     * @return void
     */
    public function attachValidator(FormInterface $form, $reference, $validator)
    {
        /** @var InputFilterInterface $filter */
        list(, $filter, $field) = $this->getElementAndInputParents($form, $form->getInputFilter(), $reference);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $filter->get($field)->getValidatorChain();

        $validatorChain->attach($validator);
    }

    /**
     * Get Validator
     *
     * @param \Zend\Form\FormInterface $form           Form
     * @param string                   $reference      Field Ref
     * @param string                   $validatorClass Validator Class
     *
     * @return null
     */
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
     * @param \Zend\Form\Element $field Field
     *
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
     * @param array               $data     Companies House 'AddressLine' data
     *
     * @return \Zend\Form\Fieldset
     */
    public function populateRegisteredAddressFieldset($fieldset, $data)
    {
        // parse out postcode from address data
        $postcode = '';
        $postcodeValidator = new PostcodeValidator(['locale' => 'en-GB']);
        foreach ($data as $key => $datum) {
            if ($postcodeValidator->isValid($datum)) {
                $postcode = $datum;
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
     * @param \Zend\Form\FormInterface $form Form
     * @param array                    $data The form data to save
     *
     * @return void
     */
    public function saveFormState(Form $form, $data)
    {
        $sessionContainer = new \Zend\Session\Container('form_state');
        $sessionContainer->offsetSet($form->getName(), $data);
    }

    /**
     * Restore form state
     *
     * @param \Zend\Form\FormInterface $form Form
     *
     * @return void
     */
    public function restoreFormState(Form $form)
    {
        $sessionContainer = new \Zend\Session\Container('form_state');
        if ($sessionContainer->offsetExists($form->getName())) {
            $form->setData($sessionContainer->offsetGet($form->getName()));
        }
    }

    /**
     * Remove Value Option
     *
     * @param \Zend\Form\Element\(Select|Radio) $element Element (Select|Radio)
     * @param string                            $key     Key
     *
     * @return void
     */
    public function removeValueOption(Element $element, $key)
    {
        $options = $element->getValueOptions();

        unset($options[$key]);

        $element->setValueOptions($options);
    }
}
