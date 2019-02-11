<?php

namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Regex as RegexValidator;
use Common\Form\Elements\Validators\NoOfPermitsMin as NoOfPermitsMinValidator;
use Common\Form\Elements\Validators\NoOfPermitsMax as NoOfPermitsMaxValidator;
use Common\Form\Elements\Validators\NoOfPermitsNotEmpty as NoOfPermitsNotEmptyValidator;

/**
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermits extends ZendElement implements InputProviderInterface
{
    protected $attributes = [
        'type' => 'number',
    ];

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'type' => \Zend\InputFilter\Input::class,
            'name' => $this->getName(),
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim']
            ],
            'required' => true,
            'validators' => [
                new NoOfPermitsNotEmptyValidator(),
                // note: this regex passes when negative numbers are passed in
                new RegexValidator('(^-?\d*(\.\d+)?$)'),
                new NoOfPermitsMinValidator(),
                new NoOfPermitsMaxValidator($this->attributes['max'])
            ]
        ];
    }
}
