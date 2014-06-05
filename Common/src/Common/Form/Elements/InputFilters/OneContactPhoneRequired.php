<?php

/**
 * One contact phone required
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * One contact phone required
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class OneContactPhoneRequired extends ZendElement\Hidden implements InputProviderInterface
{

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => false,
            'allow_empty' => true,
            'validators' => [
                $this->getCallbackValidator(),
            ]
        ];

        return $specification;
    }

    /**
     * Returns callback validator, which checks if at least one value is greater than zero
     *
     * @return \Zend\Validator\Callback
     */
    protected function getCallbackValidator()
    {
        $validator = new ZendValidator\Callback(
            function ($value, $context) {

                unset($value);
                // check if at least one of three phone inputs is populated
                $charsCount = strlen($context['phone_business'])
                    + strlen($context['phone_home'])
                    + strlen($context['phone_mobile'])
                    + strlen($context['phone_fax']);

                return ($charsCount > 0);
            }
        );

        $validator->setMessage('At least one telephone number is required');
        return $validator;
    }
}
