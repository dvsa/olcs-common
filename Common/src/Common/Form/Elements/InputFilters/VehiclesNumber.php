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

class VehiclesNumber extends ZendElement implements InputProviderInterface
{
    /**
     * Fields to compare
     * 
     * @var array
     */
    protected $elementsToCompare = ['no-of-vehicles', 'no-of-trailers'];
    
    /**
     * Validation messages
     * 
     * @var array of messages
     */
    protected $elementMessages = array(
    	'no-of-vehicles' => 'No. of vehicles can not be zero if no. of trailers equals zero',
        'no-of-trailers' => 'No. of trailers can not be zero if no. of vehicles equals zero',
    );
        
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                new ZendValidator\Digits(),
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
        $validator = new ZendValidator\Callback(function($value, $context){
            //at least one value is greater than 0
            if ($value > 0){
                return true;
            }
            //do not compare against current element
            unset($this->elementsToCompare[array_search($this->getName(), $this->elementsToCompare)]);
            
            foreach ($this->elementsToCompare as $compElement){
                if (isset($context[$compElement]) && $context[$compElement] > 0)
                    return true;
            }
            
        	//all 0 - element value is invalid
        	return false;
        }); 
        
        //set messages if exsist
        if (!isset($this->elementMessages[$this->getName()]))
            return $validator;
        
        $validator->setMessage($this->elementMessages[$this->getName()]);
        return $validator;
    }
}