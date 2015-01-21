<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Type of Licence Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractTypeOfLicenceControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTypeOfLicenceController');
    }

    /**
     * @todo These tests require a real service manager to run, as they are not mocking all dependencies,
     * these tests should be addresses
     */
    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    /**
     * @group lva-type-of-licence
     */
    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'type-of-licence' => [
                        'operator-location' => 'x',
                        'operator-type' => 'y',
                        'licence-type' => 'z'
                    ]
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }


    /**
     * @group lva-type-of-licence
     */
    public function testGetIndexActionWithAdapter()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'type-of-licence' => [
                        'operator-location' => 'x',
                        'operator-type' => 'y',
                        'licence-type' => 'z'
                    ]
                ]
            );

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(2);

        $adapter->shouldReceive('alterForm')
            ->with($form, 2, '')
            ->andReturn($form)
            ->shouldReceive('setMessages');

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithInvalidData()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost();

        $form->shouldReceive('setData')
            ->with([])
            ->andReturn($form);

        $form->shouldReceive('isValid')
            ->andReturn(false);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('type_of_licence', $this->view);
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithValidData()
    {
        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ]
            ]
        );

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $lEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'id' => 7,
                    'version' => '',
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn'
                ]
            )
            ->getMock();

        $this->sut
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($lEntity)
            ->shouldReceive('postSave')
            ->with('type_of_licence')
            ->shouldReceive('completeSection')
            ->with('type_of_licence')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    /**
     * @group lva-type-of-licence
     */
    public function testSetTypeOfLicenceAdapter()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');

        $this->assertNull($this->sut->getAdapter());

        $this->sut->setAdapter($adapter);

        $this->assertSame($adapter, $this->sut->getAdapter());
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithValidDataWithAdapterWithoutChanges()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $stubbedCurrentData = [
            'version' => 1,
            'niFlag' => 'N',
            'goodsOrPsv' => 'lcat_gv',
            'licenceType' => 'ltyp_sn'
        ];

        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ]
            ]
        );

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $lEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'id' => 7,
                    'version' => '',
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn'
                ]
            )
            ->getMock();

        $this->sut
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($lEntity)
            ->shouldReceive('postSave')
            ->with('type_of_licence')
            ->shouldReceive('completeSection')
            ->with('type_of_licence')
            ->andReturn('complete');

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedCurrentData);

        $adapter->shouldReceive('doesChangeRequireConfirmation')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                $stubbedCurrentData
            )
            ->andReturn(false)
            ->shouldReceive('processChange')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                $stubbedCurrentData
            )
            ->andReturn(false)
            ->shouldReceive('isCurrentDataSet')
            ->with($stubbedCurrentData)
            ->andReturn(true)
            ->shouldReceive('alterForm')
            ->with($form, 7, "")
            ->andReturn($form);

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithValidDataWithAdapterWithChangesRequiringConfirmation()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $stubbedCurrentData = [
            'version' => 1,
            'niFlag' => 'Y',
            'goodsOrPsv' => 'lcat_gv',
            'licenceType' => 'ltyp_sn'
        ];

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ]
            ]
        );

        $this->sut->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedCurrentData);

        $adapter->shouldReceive('doesChangeRequireConfirmation')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                $stubbedCurrentData
            )
            ->andReturn(true)
            ->shouldReceive('getRouteParams')
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getQueryParams')
            ->andReturn(['query' => ['bar' => 'baz']]);

        $response = m::mock('\Zend\Http\Response');

        $this->sut->shouldReceive('redirect->toRoute')
            ->with(null, ['foo' => 'bar'], ['query' => ['bar' => 'baz']], true)
            ->andReturn($response);

        $this->assertSame($response, $this->sut->indexAction());
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithValidDataWithAdapterWithChangesRequiringProcessing()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $stubbedCurrentData = [
            'version' => 1,
            'niFlag' => 'N',
            'goodsOrPsv' => 'lcat_gv',
            'licenceType' => 'ltyp_sn'
        ];

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_si'
                ]
            ]
        );

        $this->sut->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedCurrentData);

        $adapter->shouldReceive('doesChangeRequireConfirmation')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_si'
                ],
                $stubbedCurrentData
            )
            ->andReturn(false)
            ->shouldReceive('processChange')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_si'
                ],
                $stubbedCurrentData
            )
            ->andReturn(true);

        $response = m::mock('\Zend\Http\Response');

        $this->sut->shouldReceive('completeSection')
            ->with('type_of_licence')
            ->andReturn($response);

        $this->assertSame($response, $this->sut->indexAction());
    }

    /**
     * @group lva-type-of-licence
     */
    public function testPostWithValidDataWithAdapterWithFirstSave()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $stubbedCurrentData = [
            'version' => 1,
            'niFlag' => null,
            'goodsOrPsv' => null,
            'licenceType' => null
        ];

        $form = $this->createMockForm('Lva\TypeOfLicence');

        $this->setPost(
            [
                'version' => '',
                'type-of-licence' => [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ]
            ]
        );

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $lEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'id' => 7,
                    'version' => '',
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn'
                ]
            )
            ->getMock();

        $this->sut
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($lEntity)
            ->shouldReceive('postSave')
            ->with('type_of_licence')
            ->shouldReceive('completeSection')
            ->with('type_of_licence')
            ->andReturn('complete');

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedCurrentData);

        $adapter->shouldReceive('doesChangeRequireConfirmation')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                $stubbedCurrentData
            )
            ->andReturn(false)
            ->shouldReceive('processChange')
            ->with(
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                $stubbedCurrentData
            )
            ->andReturn(false)
            ->shouldReceive('isCurrentDataSet')
            ->with($stubbedCurrentData)
            ->andReturn(false)
            ->shouldReceive('processFirstSave')
            ->with(7)
            ->shouldReceive('alterForm')
            ->with($form, 7, '')
            ->andReturn($form);

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    /**
     * @group lva-type-of-licence
     */
    public function testConfirmationActionWithRedirect()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $response = m::mock('\Zend\Http\Response');

        $adapter->shouldReceive('confirmationAction')
            ->andReturn($response);

        $this->assertSame($response, $this->sut->confirmationAction());
    }

    /**
     * @group lva-type-of-licence
     */
    public function testConfirmationAction()
    {
        $adapter = m::mock('\Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface');
        $this->sut->setAdapter($adapter);

        $response = m::mock('\Zend\Form\Form');

        $adapter->shouldReceive('confirmationAction')
            ->andReturn($response)
            ->shouldReceive('getConfirmationMessage')
            ->andReturn('type_of_licence_confirmation')
            ->shouldReceive('getExtraConfirmationMessage')
            ->andReturn('application_type_of_licence_confirmation_subtitle');

        $this->sut->shouldReceive('render')
            ->with(
                'type_of_licence_confirmation',
                $response,
                ['sectionText' => 'application_type_of_licence_confirmation_subtitle']
            )
            ->andReturn('RESPONSE');

        $this->assertSame('RESPONSE', $this->sut->confirmationAction());
    }

    /**
     * @group lva-type-of-licence
     */
    public function testConfirmationActionWithoutAdapter()
    {
        $this->sut->shouldReceive('notFoundAction')
            ->andReturn(404);

        $this->assertSame(404, $this->sut->confirmationAction());
    }
}
