<?php
/**
 * 
 * @author Jakub.Igla <jakub.igla@valtech.co.uk
 *
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

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
        $validator = new ZendValidator\Callback(function ($value, $context) {

            // check if at least one of three phone inputs is populated
            $charsCount = strlen($context['phone_business'])
                + strlen($context['phone_home'])
                + strlen($context['phone_mobile']);

            return ($charsCount > 0);

        });

        $validator->setMessage('Please enter at least one telephone number');
        return $validator;
    }
}
