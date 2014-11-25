<?php

namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Controller\Lva\AbstractLvaControllerTestCase;
use \Mockery as m;

class LicenceOperatingCentresControllerTraitTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\CommonTest\Controller\Lva\Traits\Stubs\LicenceOperatingCentresControllerTraitStub');
    }

    public function testDisableConditionalValidation()
    {
        $form = m::mock('\Common\Form\Form');

        $trailerInput = m::mock();

        $inputFilter = m::mock()
            ->shouldReceive('get')
            ->with('totAuthVehicles')
            ->andReturn($trailerInput)
            ->getMock();

        $form->shouldReceive('getInputFilter->get')
            ->andReturn($inputFilter);

        $this->getMockFormHelper()
            ->shouldReceive('disableValidation')
            ->with($trailerInput);

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(123);

        $this->mockEntity('Licence', 'getTotalAuths')
            ->with(123)
            ->andReturn(
                [
                    'totAuthLargeVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthSmallVehicles' => null,
                    'totAuthVehicles' => 4,
                    'totAuthTrailers' => 5
                ]
            );

        $this->setPost(
            [
                'data' => [
                    // not changed
                    'totAuthVehicles' => '4',
                    // changed!
                    'totAuthTrailers' => '10',
                ]
            ]
        );
        $this->sut->callDisableConditionalValidation($form);
    }

    public function testFormatCrudDataForSaveRemovesOperatingCentreAddress()
    {
        $data = [
            'key' => 'value',
            'operatingCentre' => [
                'addresses' => 'foo'
            ]
        ];
        $expected = [
            'key' => 'value',
            'operatingCentre' => []
        ];
        $this->assertEquals(
            $expected,
            $this->sut->callFormatCrudDataForSave($data)
        );
    }
}
