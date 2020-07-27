<?php

namespace Common\Service\Qa;

use Zend\Form\Element\Text as ZendText;
use Zend\InputFilter\InputProviderInterface;

class Text extends ZendText implements InputProviderInterface
{
    protected $attributes = [
        'id' => 'qaText',
    ];

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
         return [
             'id' => 'qaText',
             'name' => $this->getName(),
             'required' => false,
             'filters' => [
                 ['name' => 'StringTrim'],
             ],
             'validators' => []
         ];
    }
}
