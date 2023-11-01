<?php

namespace Common\Form;

use Common\Service\Qa\IsValidHandlerInterface;
use Common\Service\Qa\DataHandlerInterface;
use RuntimeException;
use Laminas\Form\Fieldset;

class QaForm extends BaseQaForm
{
    const QUESTION_FIELDSET_PREFIX = 'fieldset';

    /** @var array */
    private $applicationStep;

    /** @var array */
    private $dataHandlers = [];

    /** @var array */
    private $isValidHandlers = [];

    /** @var bool */
    private $successfulValidationAllowed = true;

    /**
     * Allow validators to run by filling in missing keys in input data
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $data = $this->updateDataForQa($data);
        $this->callParentSetData($data);
    }

    /**
     * Prepare form for redisplay by calling any required dataHandler instances appropriate to the form control type
     *
     * @param mixed $data
     */
    public function setDataForRedisplay($data)
    {
        $this->setData($data);

        $applicationStepType = $this->applicationStep['type'];
        if (!isset($this->dataHandlers[$applicationStepType])) {
            return;
        }

        $dataHandler = $this->dataHandlers[$applicationStepType];
        $dataHandler->setData($this);
    }

    /**
     * Whether the form passes validation
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $isValid = $this->callParentIsValid();

        if (!$isValid || !$this->successfulValidationAllowed) {
            return false;
        }

        $applicationStepType = $this->applicationStep['type'];
        if (!isset($this->isValidHandlers[$applicationStepType])) {
            return true;
        }

        $isValidHandler = $this->isValidHandlers[$applicationStepType];
        return $isValidHandler->isValid($this);
    }

    /**
     * Prevent form from successfully validating (i.e. returning true from isValid) even when all fields are valid
     */
    public function preventSuccessfulValidation()
    {
        $this->successfulValidationAllowed = false;
    }

    /**
     * Set the application step data provided by the backend
     *
     * @param array $applicationStep
     */
    public function setApplicationStep(array $applicationStep)
    {
        $this->applicationStep = $applicationStep;
    }

    /**
     * Get the application step data
     *
     * @return array
     */
    public function getApplicationStep()
    {
        return $this->applicationStep;
    }

    /**
     * Add a custom setData handler to be run for the specified custom form control type
     *
     * @param string $type
     * @param DataHandlerInterface $dataHandler
     */
    public function registerDataHandler($type, DataHandlerInterface $dataHandler)
    {
        $this->dataHandlers[$type] = $dataHandler;
    }

    /**
     * Add a custom isValid handler to be run for the specified custom form control type
     *
     * @param string $type
     * @param IsValidHandlerInterface $isValidHandler
     */
    public function registerIsValidHandler($type, IsValidHandlerInterface $isValidHandler)
    {
        $this->isValidHandlers[$type] = $isValidHandler;
    }

    /**
     * Get the subset of form data representing the Q&A question fieldset
     *
     * @return array
     */
    public function getQuestionFieldsetData()
    {
        $questionFieldsetName = $this->getQuestionFieldsetName();

        return $this->data[self::QA_FIELDSET_NAME][$questionFieldsetName];
    }

    /**
     * Get the Fieldset object representing the Q&A question
     *
     * @return Fieldset
     */
    public function getQuestionFieldset()
    {
        $questionFieldsetName = $this->getQuestionFieldsetName();

        return $this->get(self::QA_FIELDSET_NAME)->get($questionFieldsetName);
    }

    /**
     * Get the name of the fieldset that contains the Q&A question
     *
     * @return Fieldset
     */
    private function getQuestionFieldsetName()
    {
        foreach ($this->get(self::QA_FIELDSET_NAME)->getFieldsets() as $fieldset) {
            $fieldsetName = $fieldset->getName();
            $fieldsetPrefix = substr($fieldsetName, 0, strlen(self::QUESTION_FIELDSET_PREFIX));
            if ($fieldsetPrefix == self::QUESTION_FIELDSET_PREFIX) {
                return $fieldsetName;
            }
        }

        throw new RuntimeException('Unable to locate question fieldset in form');
    }

    /**
     * Call the isValid function of the parent class (to assist in unit testing)
     *
     * @return bool
     */
    protected function callParentIsValid()
    {
        return parent::isValid();
    }

    /**
     * Call the setData function of the parent class (to assist in unit testing)
     *
     * @param mixed $data
     */
    protected function callParentSetData($data)
    {
        return parent::setData($data);
    }
}
