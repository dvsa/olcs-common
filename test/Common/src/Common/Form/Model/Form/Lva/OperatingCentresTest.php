<?php

namespace CommonTest\Form\Model\Form\Lva;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;

/**
 * Class OperatingCentresTest
 *
 * @package OlcsTest\FormTest
 * @group ComponentTests
 * @group FormTests
 * @note this doesn't extend AbstractFormTest as we're only testing filters,
 * not validation
 */
class OperatingCentresTest extends MockeryTestCase
{
    protected $formName = '\Common\Form\Model\Form\Lva\OperatingCentres';

    protected $sm;

    protected $form;

    public function setUp()
    {
        parent::setUp();

        $this->sm   = Bootstrap::getRealServiceManager();
        $this->form = $this->sm->get('FormAnnotationBuilder')->createForm($this->formName);
        $this->form->remove('dataTrafficArea');
    }

    /**
     * @dataProvider numericFieldsProvider
     */
    public function testNumericFieldsAreNulled($inputData, $expectedData)
    {
        $formData = [
            'data' => $inputData
        ];

        $this->form->setData($formData);

        $this->assertTrue($this->form->isValid(), json_encode($this->form->getMessages()));

        $filtered = $this->form->getData();
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
                ],
                [
                    'totAuthSmallVehicles'  => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles'  => null,
                    'totAuthVehicles'       => null,
                    'totAuthTrailers'       => null,
                    'totCommunityLicences'  => null,
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
                ],
                [
                    'totAuthSmallVehicles'  => '0',
                    'totAuthMediumVehicles' => '0',
                    'totAuthLargeVehicles'  => '0',
                    'totAuthVehicles'       => '0',
                    'totAuthTrailers'       => '0',
                    'totCommunityLicences'  => '0',
                ],
            ],
        ];
    }
}
