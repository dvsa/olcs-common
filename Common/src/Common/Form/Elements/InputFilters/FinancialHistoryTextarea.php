<?php
/**
 * Input Specification for Finacial History additional info
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Textarea as ZendElement;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator;

/**
 * Input Specification for Finacial History additional info
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class FinancialHistoryTextarea extends ZendElement implements InputProviderInterface
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

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
                new Validator\NotEmpty(Validator\NotEmpty::NULL),
                new \Common\Form\Elements\Validators\FHAdditionalInfo,
            ]
        ];
        
        return $specification;
    }

}