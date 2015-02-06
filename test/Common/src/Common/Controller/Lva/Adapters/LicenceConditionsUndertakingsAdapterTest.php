<?php

/**
 * Licence Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Controller\Lva\Adapters\LicenceConditionsUndertakingsAdapter;

/**
 * Licence Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceConditionsUndertakingsAdapter();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testSave()
    {
        $data = [
            'foo' => 'bar'
        ];

        $expectedData = [
            'foo' => 'bar',
            'addedVia' => ConditionUndertakingEntityService::ADDED_VIA_LICENCE
        ];

        // Mocks
        $entityService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $entityService);

        // Expectations
        $entityService->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 123]);

        $this->assertEquals(123, $this->sut->save($data));
    }

    public function testGetTableData()
    {
        $id = 1;

        $this->assertEquals([], $this->sut->getTableData($id));
    }

    public function testProcessDataForSave()
    {
        $id = 123;
        $data = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
            ]
        ];
        $expected = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE,
                'operatingCentre' => null,
                'licence' => 123,
                'isDraft' => 'N'
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }

    /**
     * Need to test this to test getLicenceId
     */
    public function testAlterForm()
    {
        // Params
        $form = m::mock('\Zend\Form\Form');
        $id = 321;
        $licenceId = 321;
        $stubbedOcList = [
            'Results' => [
                [
                    'operatingCentre' => [
                        'id' => 987,
                        'address' => [
                            'addressLine1' => '123 street',
                            'addressLine2' => 'foo bar town'
                        ]
                    ]
                ]
            ]
        ];
        $expectedOptions = [
            'Licence' => [
                'label' => 'Licence',
                'options' => [
                    ConditionUndertakingEntityService::ATTACHED_TO_LICENCE => 'Licence (654)'
                ]
            ],
            'OC' => [
                'label' => 'OC Address',
                'options' => [
                    987 => '123 street, foo bar town'
                ]
            ]
        ];

        // Mocks
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockLoc = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLoc);

        // Expectations
        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn(['licNo' => 654]);

        $mockLoc->shouldReceive('getOperatingCentreListForLva')
            ->with($id)
            ->andReturn($stubbedOcList);

        $form->shouldReceive('get')
            ->with('fields')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('attachedTo')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setValueOptions')
                    ->with($expectedOptions)
                    ->getMock()
                )
                ->getMock()
            );

        $this->sut->alterForm($form, $id);
    }
}
