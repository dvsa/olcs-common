<?php

namespace CommonTest\Form\Model\Fieldset;

use Common\Form\Model\Fieldset\HoursOfWeekContentRequired as HoursOfWeekContentRequiredFieldset;
use Common\Form\Model\Fieldset\HoursOfWeekContentRequired;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class HoursOfWeekContentRequiredTest
 * @package CommonTest\Form\Model\Fieldset
 *
 * @covers Common\Form\Model\Fieldset
 */
class HoursOfWeekContentRequiredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HoursOfWeekContentRequiredFieldset
     */
    protected $fieldset;

    public function setUp()
    {
        $this->fieldset = new HoursOfWeekContentRequired();
    }

    /**
     * @param $context
     * @param bool|false $isValid
     *
     * @dataProvider getDataProviderForFieldset
     */
    public function testFieldset($context, $isValid = false)
    {
        $form = new Form();

        $builder = new AnnotationBuilder();
        $fieldset = $builder->createForm($this->fieldset);

        $form->add($fieldset);

        $form->setData($context);

        if ($isValid === false) {
            $this->assertFalse($form->isValid());
        } else {
            $this->assertTrue($form->isValid());
        }
    }

    /**
     * 1. Data for mock form
     * 2. Expected isValid response
     *
     * @return array
     */
    public function getDataProviderForFieldset()
    {
        return [
            // Data sets that match requirements for the Hours Per Week fieldset
            // which uses this SumContext validator.
            'Hours Per Week invalid example with all contexts are null' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => null,
                        'hoursTue' => null,
                        'hoursWed' => null,
                        'hoursThu' => null,
                        'hoursFri' => null,
                        'hoursSat' => null,
                        'hoursSun' => null,
                    ],
                ],
                false,
            ],
            'Hours Per Week invalid example with a zero digit in any 1 context' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => null,
                        'hoursTue' => 0,
                        'hoursWed' => null,
                        'hoursThu' => null,
                        'hoursFri' => null,
                        'hoursSat' => null,
                        'hoursSun' => null,
                    ],
                ],
                false,
            ],
            'Hours Per Week invalid example with a zero digit in any other context' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => null,
                        'hoursTue' => null,
                        'hoursWed' => null,
                        'hoursThu' => null,
                        'hoursFri' => null,
                        'hoursSat' => null,
                        'hoursSun' => 0,
                    ],
                ],
                false,
            ],
            'Hours Per Week valid example with one float value, rest are null' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => null,
                        'hoursTue' => null,
                        'hoursWed' => null,
                        'hoursThu' => null,
                        'hoursFri' => null,
                        'hoursSat' => null,
                        'hoursSun' => 1.4,
                    ],
                ],
                true,
            ],
            'Hours Per Week valid example with two float values, rest are null' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => null,
                        'hoursTue' => null,
                        'hoursWed' => 1.3,
                        'hoursThu' => null,
                        'hoursFri' => null,
                        'hoursSat' => null,
                        'hoursSun' => 0.4,
                    ],
                ],
                true,
            ],
            'Hours Per Week valid example with all float value, none are null' => [
                [
                    'hoursOfWeekContent' => [
                        'hoursMon' => 3.5,
                        'hoursTue' => 8,
                        'hoursWed' => 12,
                        'hoursThu' => 4.5,
                        'hoursFri' => 2.7,
                        'hoursSat' => 3.92,
                        'hoursSun' => 0.4,
                    ],
                ],
                true,
            ],
        ];
    }
}
