<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtNoOfPermitsEmissionsCategoryHiddenElement;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Identical;

/**
 * EcmtNoOfPermitsEmissionsCategoryHiddenElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsEmissionsCategoryHiddenElementTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $name = 'emissionsCategory';
        $expectedValue = 'euro5';

        $expectedInputSpecification = [
            'name' => $name,
            'required' => true,
            'validators' => [
                [
                    'name' => Identical::class,
                    'options' => [
                        'token' => $expectedValue,
                    ]
                ]
            ]
        ];

        $ecmtNoOfPermitsEmissionsCategoryHiddenElement = new EcmtNoOfPermitsEmissionsCategoryHiddenElement($name);
        $ecmtNoOfPermitsEmissionsCategoryHiddenElement->setOption('expectedValue', $expectedValue);

        $this->assertInstanceOf(
            InputProviderInterface::class,
            $ecmtNoOfPermitsEmissionsCategoryHiddenElement
        );

        $this->assertInstanceOf(
            Hidden::class,
            $ecmtNoOfPermitsEmissionsCategoryHiddenElement
        );

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtNoOfPermitsEmissionsCategoryHiddenElement->getInputSpecification()
        );
    }
}
