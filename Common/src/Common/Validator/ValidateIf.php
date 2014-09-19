<?php

namespace Common\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorPluginManagerAwareInterface;
use Zend\Validator\ValidatorPluginManager;

/**
 * Class ValidateIf
 * @package Common\Validator
 */
class ValidateIf extends AbstractValidator implements ValidatorPluginManagerAwareInterface
{
    const NO_CONTEXT = 'no_context';

    protected $messageTemplates = array(
        self::NO_CONTEXT         => 'Context field was not found in the input',
    );

    /**
     * @var string
     */
    protected $contextField = '';

    /**
     * @var array
     */
    protected $contextValues = [];

    /**
     * @var bool
     */
    protected $contextTruth = true;

    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @var ValidatorPluginManager
     */
    protected $validatorPluginManager;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * @param array $contextValues
     * @return $this
     */
    public function setContextValues($contextValues)
    {
        $this->contextValues = (array) $contextValues;
        return $this;
    }

    /**
     * @return array
     */
    public function getContextValues()
    {
        return $this->contextValues;
    }

    /**
     * @param string $contextField
     * @return $this
     */
    public function setContextField($contextField)
    {
        $this->contextField = $contextField;
        return $this;
    }

    /**
     * @return string
     */
    public function getContextField()
    {
        return $this->contextField;
    }

    /**
     * @param boolean $contextTruth
     * @return $this
     */
    public function setContextTruth($contextTruth)
    {
        $this->contextTruth = $contextTruth;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getContextTruth()
    {
        return $this->contextTruth;
    }

    public function setValidators(array $validators)
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param \Zend\Validator\ValidatorChain $validatorChain
     * @return $this
     */
    public function setValidatorChain($validatorChain)
    {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    /**
     * @return \Zend\Validator\ValidatorChain
     */
    public function getValidatorChain()
    {
        if (is_null($this->validatorChain)) {
            $this->validatorChain = new ValidatorChain();
            $this->validatorChain->setPluginManager($this->getValidatorPluginManager());
            foreach ($this->getValidators() as $validator) {
                $this->validatorChain->attachByName(
                    $validator['name'],
                    isset($validator['options']) ? $validator['options'] : [],
                    isset($validator['break_chain_on_failure']) ? $validator['break_chain_on_failure'] : false
                );
            }
        }

        return $this->validatorChain;
    }

    /**
     * @param ValidatorPluginManager $validatorPluginManager
     * @return $this
     */
    public function setValidatorPluginManager(ValidatorPluginManager $validatorPluginManager)
    {
        $this->validatorPluginManager = $validatorPluginManager;
        return $this;
    }

    /**
     * @return ValidatorPluginManager
     */
    public function getValidatorPluginManager()
    {
        return $this->validatorPluginManager;
    }


    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param null $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if (array_key_exists($this->getContextField(), $context)) {
            if (!(in_array($context[$this->getContextField()], $this->getContextValues()) ^ $this->getContextTruth())) {
                $result = $this->getValidatorChain()->isValid($value, $context);
                if (!$result) {
                    $this->abstractOptions['messages'] = $this->getValidatorChain()->getMessages();
                }
                return $result;
            } else {
                return true;
            }
        }

        $this->error(self::NO_CONTEXT);
        return false;
    }

    public function setOptions($options = [])
    {
        if (isset($options['context_field'])) {
            $this->setContextField($options['context_field']);
        }

        if (isset($options['context_truth'])) {
            $this->setContextTruth($options['context_truth']);
        }

        if (isset($options['context_values'])) {
            $this->setContextValues($options['context_values']);
        }

        return parent::setOptions($options);
    }
}
