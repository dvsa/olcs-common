<?php

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;
use Zend\Http\Request;
use Common\Form\Elements\Types\Address;
use Zend\Form\Element\Checkbox;


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

    /**
     * Create a form
     *
     * @param string $formName
     * @return \Zend\Form\Form
     * @throws \Exception
     */
    public function createForm($formName)
    {
        $class = $this->findForm($formName);

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');

        return $annotationBuilder->createForm($class);
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
        foreach (['Olcs', 'Common'] as $namespace) {
            $class = $namespace . '\Form\Model\Form\\' . $formName;

            if (class_exists($class)) {
                return $class;
            }
        }

        throw new \Exception('Form does not exist: ' . $formName);
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
}
