<?php

namespace Common\Form;

use Common\Form\Elements\Custom\NoOfPermits as NoOfPermitsElement;
use Laminas\Form\Fieldset;

/**
 * Class NoOfPermitsForm
 */
class NoOfPermitsForm extends Form
{
    /** @var bool */
    private $addAllFieldsZeroError;

    /**
     * Add a further error message if all fields are found to have a zero value
     *
     * @return array
     */
    public function getMessages($elementName = null)
    {
        $messages = $this->callParentGetMessages();

        if ($this->addAllFieldsZeroError) {
            $messages[] = 'permits.page.no-of-permits.error.at-least-one';
        }

        return $messages;
    }

    /**
     * Check all NoOfPermits elements if the form is found to be otherwise valid
     *
     * @return bool
     */
    public function isValid()
    {
        $this->addAllFieldsZeroError = false;

        $isValid = $this->callParentIsValid();
        if ($isValid) {
            $isValid = $this->hasNonZeroNoOfPermitsElements();
            if (!$isValid) {
                $this->addAllFieldsZeroError = true;
            }
        }

        return $isValid;
    }

    /**
     * Returns true if one or more of the NoOfPermits elements within the form has a non-zero value
     *
     * @return bool
     */
    private function hasNonZeroNoOfPermitsElements()
    {
        $noOfPermitsElements = $this->fetchNoOfPermitsElements();

        foreach ($noOfPermitsElements as $element) {
            if ($element->hasNonZeroValue()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Recursively traverse all fieldsets within the form and return an array of NoOfPermits elements found
     *
     * @return array
     */
    private function fetchNoOfPermitsElements()
    {
        $noOfPermitsElements = [];

        foreach ($this->getFieldsets() as $fieldset) {
            $noOfPermitsElements = $this->appendNoOfPermitsElementsFromFieldset($noOfPermitsElements, $fieldset);
        }

        return $noOfPermitsElements;
    }

    /**
     * Append the NoOfPermits elements from the specified fieldset and all child fieldsets to the provided array
     *
     * @param array $noOfPermitsElements
     * @param Fieldset $fieldset
     *
     * @return array
     */
    private function appendNoOfPermitsElementsFromFieldset(array $noOfPermitsElements, Fieldset $fieldset)
    {
        foreach ($fieldset->getElements() as $element) {
            if ($element instanceof NoOfPermitsElement) {
                $noOfPermitsElements[] = $element;
            }
        }

        foreach ($fieldset->getFieldsets() as $fieldset) {
            $noOfPermitsElements = $this->appendNoOfPermitsElementsFromFieldset($noOfPermitsElements, $fieldset);
        }

        return $noOfPermitsElements;
    }

    /**
     * Calls the getMessages method of the parent class. This has been separated into a new method to facilitate unit
     * testing of this class
     *
     * @return array
     */
    protected function callParentGetMessages()
    {
        return parent::getMessages();
    }

    /**
     * Calls the isValid method of the parent class. This has been separated into a new method to facilitate unit
     * testing of this class
     *
     * @return bool
     */
    protected function callParentIsValid()
    {
        return parent::isValid();
    }
}
