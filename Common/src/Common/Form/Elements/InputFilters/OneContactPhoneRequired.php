<?php

/**
 * One contact phone required
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as LaminasElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * One contact phone required
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class OneContactPhoneRequired extends LaminasElement\Hidden implements InputProviderInterface
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
     * @return \Laminas\Validator\Callback
     */
    protected function getCallbackValidator()
    {
        $validator = new LaminasValidator\Callback(
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
