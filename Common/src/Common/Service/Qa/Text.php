<?php

namespace Common\Service\Qa;

use Zend\Form\Element\Text as ZendText;
use Zend\InputFilter\InputProviderInterface;

class Text extends ZendText implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
         return [
             'name' => $this->getName(),
             'required' => false,
             'filters' => [
                 ['name' => 'StringTrim'],
             ],
             'validators' => []
         ];
    }
}
