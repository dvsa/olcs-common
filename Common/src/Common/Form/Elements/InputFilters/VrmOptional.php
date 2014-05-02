<?php

/**
 * VrmOptional InputFilter
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\Alnum;

/**
 * VrmOptional Element
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */
class VrmOptional extends Vrm implements InputProviderInterface
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
        $specification = parent::getInputSpecification();
        $specification['required'] = false;

        return $specification;
    }
}
