<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormTest;
use Olcs\TestHelpers\FormTester\Data\Object as F;
use CommonTest\Bootstrap;

/**
 * Class OperatingCentresTest
 *
 * @package OlcsTest\FormTest
 * @group ComponentTests
 * @group FormTests
 */
class OperatingCentresTest extends AbstractFormTest
{
    protected $formName = '\Common\Form\Model\Form\Lva\OperatingCentres';


    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    protected function getFormData()
    {
        $smContext    = new F\Context(new F\Stack(['data', 'totAuthSmallVehicles']) , '2');
        $medContext   = new F\Context(new F\Stack(['data', 'totAuthMediumVehicles']), '3');
        $largeContext = new F\Context(new F\Stack(['data', 'totAuthLargeVehicles']) , '4');
        $oneOCContext = new F\Context(new F\Stack(['data', 'noOfOperatingCentres']) , '1');
        $minContext   = new F\Context(new F\Stack(['data', 'minVehicleAuth'])       , '9');
        $noOCContext  = new F\Context(new F\Stack(['data', 'noOfOperatingCentres']) , '0');

        return [
            new F\Test(
                new F\Stack(['data', 'totAuthVehicles']),
                new F\Value(F\Value::INVALID, '', $smContext, $medContext, $largeContext, $oneOCContext, $minContext),
                new F\Value(F\Value::INVALID, '0', $smContext, $medContext, $largeContext, $oneOCContext, $minContext),
                new F\Value(F\Value::INVALID, '12', $smContext, $medContext, $largeContext, $oneOCContext, $minContext),
                new F\Value(F\Value::VALID, '9', $smContext, $medContext, $largeContext, $oneOCContext, $minContext),
                new F\Value(F\Value::INVALID, 'foo'),
                new F\Value(F\Value::INVALID, 'bar', $smContext, $medContext, $largeContext, $oneOCContext, $minContext),

                new F\Value(F\Value::INVALID, ''),
                new F\Value(F\Value::INVALID, '', $noOCContext)
            ),
        ];
    }

    /**
     * Test that filters nullify numeric fields correctly
     * @dataProvider numericFieldsProvider
     */
    public function testNumericFieldsAreNulled($inputData, $expectedData)
    {
        $sm = Bootstrap::getRealServiceManager();

        $form = $sm->get('FormAnnotationBuilder')->createForm($this->formName);
        $form->remove('dataTrafficArea');

        $formData = [
            'data' => $inputData
        ];

        $form->setData($formData);

        $this->assertTrue($form->isValid(), json_encode($form->getMessages()));

        $filtered = $form->getData();
        foreach ($inputData as $field => $value) {
            $this->assertEquals($expectedData[$field], $filtered['data'][$field]);
        }
    }

    public function numericFieldsProvider()
    {
        return [
            'empty strings' => [
                [
                    'totAuthSmallVehicles'  => '',
                    'totAuthMediumVehicles' => '',
                    'totAuthLargeVehicles'  => '',
                    'totAuthVehicles'       => '',
                    'totAuthTrailers'       => '',
                    'totCommunityLicences'  => '',
                    'noOfOperatingCentres'  => '1',
                ],
                [
                    'totAuthSmallVehicles'  => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles'  => null,
                    'totAuthVehicles'       => null,
                    'totAuthTrailers'       => null,
                    'totCommunityLicences'  => null,
                    'noOfOperatingCentres'  => '1',
                ],
            ],
            'zeroes' => [
                [
                    'totAuthSmallVehicles'  => '0',
                    'totAuthMediumVehicles' => '0',
                    'totAuthLargeVehicles'  => '0',
                    'totAuthVehicles'       => '0',
                    'totAuthTrailers'       => '0',
                    'totCommunityLicences'  => '0',
                    'noOfOperatingCentres'  => '1',
                ],
                [
                    'totAuthSmallVehicles'  => '0',
                    'totAuthMediumVehicles' => '0',
                    'totAuthLargeVehicles'  => '0',
                    'totAuthVehicles'       => '0',
                    'totAuthTrailers'       => '0',
                    'totCommunityLicences'  => '0',
                    'noOfOperatingCentres'  => '1',
                ],
            ],
        ];
    }
}