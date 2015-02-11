<?php

/**
 * Abstract Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Abstract Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        // Mock it as it is abstract
        $this->sut = m::mock('\Common\Controller\Lva\Adapters\AbstractConditionsUndertakingsAdapter')
            ->makePartial()
            // We need to mock some unimplemented abstract methods
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();
        $this->sm->setService('Script', $mockScript);

        $mockScript->shouldReceive('loadFile')
            ->with('lva-crud');

        $this->sut->attachMainScripts();
    }

    public function testCanEditRecord()
    {
        $this->assertTrue($this->sut->canEditRecord(1, 2));
    }

    public function testGetTableName()
    {
        $this->assertEquals('lva-conditions-undertakings', $this->sut->getTableName());
    }

    public function testAlterTable()
    {
        $table = m::mock('\Common\Service\Table\TableBuilder');
        $table->shouldReceive('removeAction')->with('restore');

        $this->sut->alterTable($table);
    }

    public function testSave()
    {
        $data = [
            'foo' => 'bar'
        ];

        // Mocks
        $entityService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $entityService);

        // Expectations
        $entityService->shouldReceive('save')
            ->with($data)
            ->andReturn(['id' => 123]);

        $this->assertEquals(123, $this->sut->save($data));
    }

    public function testSaveUpdate()
    {
        $data = [
            'id' => 123,
            'foo' => 'bar'
        ];

        // Mocks
        $entityService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $entityService);

        // Expectations
        $entityService->shouldReceive('save')
            ->with($data)
            ->andReturn([]);

        $this->assertEquals(123, $this->sut->save($data));
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
                'operatingCentre' => null
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForSaveWithOperatingCentre()
    {
        $id = 123;
        $data = [
            'fields' => [
                'attachedTo' => 'foo'
            ]
        ];
        $expected = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE,
                'operatingCentre' => 'foo'
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForForm()
    {
        $data = [
            'foo' => 'bar'
        ];
        $expected = [
            'foo' => 'bar'
        ];

        $return = $this->sut->processDataForForm($data);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForFormWithAttachedToLicence()
    {
        $data = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
            ],
            'foo' => 'bar'
        ];
        $expected = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
            ],
            'foo' => 'bar'
        ];

        $return = $this->sut->processDataForForm($data);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForFormWithAttachedToOc()
    {
        $data = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE,
                'operatingCentre' => 'foo'
            ],
            'foo' => 'bar'
        ];
        $expected = [
            'fields' => [
                'attachedTo' => 'foo',
                'operatingCentre' => 'foo'
            ],
            'foo' => 'bar'
        ];

        $return = $this->sut->processDataForForm($data);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForFormWithAttachedToOcWithoutOc()
    {
        $data = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE
            ],
            'foo' => 'bar'
        ];
        $expected = [
            'fields' => [
                'attachedTo' => ''
            ],
            'foo' => 'bar'
        ];

        $return = $this->sut->processDataForForm($data);

        $this->assertEquals($expected, $return);
    }

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
        $mockEntityService = m::mock();

        // Expectations
        $this->sut->shouldReceive('getLicenceId')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('getLvaOperatingCentreEntityService')
            ->andReturn($mockEntityService);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn(['licNo' => 654]);

        $mockEntityService->shouldReceive('getOperatingCentreListForLva')
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

    public function testAlterFormWithoutOcs()
    {
        // Params
        $form = m::mock('\Zend\Form\Form');
        $id = 123;
        $licenceId = 321;
        $stubbedOcList = [
            'Results' => [

            ]
        ];
        $expectedOptions = [
            'Licence' => [
                'label' => 'Licence',
                'options' => [
                    ConditionUndertakingEntityService::ATTACHED_TO_LICENCE => 'Licence (654)'
                ]
            ]
        ];

        // Mocks
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockEntityService = m::mock();

        // Expectations
        $this->sut->shouldReceive('getLicenceId')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('getLvaOperatingCentreEntityService')
            ->andReturn($mockEntityService);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn(['licNo' => 654]);

        $mockEntityService->shouldReceive('getOperatingCentreListForLva')
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
