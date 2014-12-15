<?php

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\Http\Request;
use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\Form\Element\Checkbox;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;
use Common\Form\Elements\Types\Address;
use Common\Service\Table\TableBuilder;
use Zend\Form\Element;
use Zend\Form\Element\DateSelect;
use Zend\InputFilter\Input;
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

    const CSRF_TIMEOUT = 600;

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
        $class = $this->findForm($formName);

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');

        $form = $annotationBuilder->createForm($class);

        if ($addCsrf) {
            $config = array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'security',
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
                'name' => 'form-actions[submit]',
                'options' => array(
                    'label' => 'Continue'
                ),
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'visually-hidden'
                )
            );
            $form->add($config);
        }

        return $form;
    }

    public function setFormActionFromRequest($form, $request)
    {
        if (!$form->hasAttribute('action')) {
            $form->setAttribute('action', $request->getUri()->getPath());
        }
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
        $return = false;

        $post = (array)$request->getPost();

        $fieldsets = $form->getFieldsets();

        foreach ($fieldsets as $fieldset) {

            if ($fieldset instanceof Address && $this->processAddressLookupFieldset($fieldset, $post, $form)) {
                // @NOTE we can't just return true here, as any other address lookups need processing also
                $return = true;
            }
        }

        return $return;
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

        // If we have clicked the find address button
        if (isset($post[$name]['searchPostcode']['search']) && !empty($post[$name]['searchPostcode']['search'])) {

            $this->processPostcodeSearch($fieldset, $post, $name);
            return true;
        }

        // If we have selected an address
        if (isset($post[$name]['searchPostcode']['select']) && !empty($post[$name]['searchPostcode']['select'])) {

            $this->processAddressSelect($fieldset, $post, $name, $form);
            $this->removeAddressSelectFields($fieldset);

            return true;
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

        $addressList = $this->getServiceLocator()->get('Data\Address')->getAddressesForPostcode($postcode);

        // If we haven't found any addresses
        if (empty($addressList)) {

            $this->removeAddressSelectFields($fieldset);

            $fieldset->get('searchPostcode')->setMessages(array('No addresses found for postcode'));

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
    private function processAddressSelect($fieldset, $post, $name, $form)
    {
        $address = $this->getServiceLocator()->get('Data\Address')
            ->getAddressForUprn($post[$name]['searchPostcode']['addresses']);

        $addressDetails = $this->getServiceLocator()->get('Helper\Address')->formatPostalAddressFromBs7666($address);

        $data = $post;
        $data[$name] = $addressDetails;
        $form->setData($data);
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
        if (in_array($type, array(self::ALTER_LABEL_APPEND, self::ALTER_LABEL_PREPEND))) {
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

    private function removeElement($form, $filter, $elementReference)
    {
        if (strstr($elementReference, '->')) {
            list($container, $elementReference) = explode('->', $elementReference, 2);

            $this->removeElement($form->get($container), $filter->get($container), $elementReference);
        } else {
            $form->remove($elementReference);
            $filter->remove($elementReference);
        }
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

                $this->disableEmptyValidation($fieldset, $filter->get($fieldset->getName()));
            }
        }
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

        if (strstr($reference, '->')) {
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
     * @param string $fieldset
     * @return boolean
     */
    public function processCompanyNumberLookupForm(Form $form, $data, $fieldset)
    {
        if (strlen(trim($data[$fieldset]['companyNumber']['company_number'])) === 8) {

            $result = $this->getServiceLocator()
                ->get('Data\CompaniesHouse')
                ->search('numberSearch', $data[$fieldset]['companyNumber']['company_number']);

            if ($result['Count'] === 1) {

                $form->get($fieldset)->get('name')->setValue($result['Results'][0]['CompanyName']);
                return;
            }

            $message = 'company_number.search_no_results.error';
        } else {
            $message = 'company_number.length.validation.error';
        }

        $translator = $this->getServiceLocator()->get('translator');

        $form->get($fieldset)->get('companyNumber')->setMessages(
            array(
                'company_number' => array($translator->translate($message))
            )
        );
    }
}
