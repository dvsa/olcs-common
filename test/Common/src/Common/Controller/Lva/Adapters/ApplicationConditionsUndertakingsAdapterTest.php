<?php

/**
 * Application Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Controller\Lva\Adapters\ApplicationConditionsUndertakingsAdapter;

/**
 * Application Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationConditionsUndertakingsAdapter();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testSave()
    {
        $data = [
            'foo' => 'bar'
        ];

        $expectedData = [
            'foo' => 'bar',
            'addedVia' => ConditionUndertakingEntityService::ADDED_VIA_APPLICATION
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

        // Mocks
        $mockEntity = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockEntity);

        // Expectations
        $mockEntity->shouldReceive('getForApplication')
            ->with($id)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getTableData($id));
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
                'application' => 123,
                'isDraft' => 'Y',
                'action' => 'A'
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
        $id = 123;
        $licenceId = 321;
        $stubbedOcList = [
            'Results' => [
                [
                    'operatingCentre' => [
                        'id' => 987,
                        'address' => [
                            'addressLine1' => '123 street',
                            'town' => 'foo bar town'
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
        $mockAoc = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAoc);

        // Expectations
        $mockApplicationEntity->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn(['licNo' => 654]);

        $mockAoc->shouldReceive('getOperatingCentreListForLva')
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
