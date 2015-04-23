<?php

/**
 * Abstract Vehicles Controller Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Vehicles Controller Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractVehiclesControllerTest extends MockeryTestCase
{
    protected $adapter;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('\Common\Controller\Lva\AbstractVehiclesController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');
        $this->sut->setAdapter($this->adapter);
    }

    /**
     * @group abstractVehiclesController
     */
    public function testTransferAction()
    {
        $licenceId = 1;
        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(false)
            ->getMock();

        $mockForm = m::mock()
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('licence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setValueOptions')
                    ->with('licences')
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\VehiclesTransfer')
            ->andReturn($mockForm)
            ->once()
            ->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $mockRequest)
            ->once()
            ->getMock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getOtherActiveLicences')
            ->with($licenceId)
            ->andReturn('licences')
            ->getMock()
        );

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromRoute')
                ->with('licence')
                ->andReturn($licenceId)
                ->once()
                ->getMock()
            )
            ->shouldReceive('renderForm')
            ->with($mockForm)
            ->andReturn('render');

        $this->assertEquals('render', $this->sut->transferAction());
    }

    /**
     * @group abstractVehiclesController
     */
    public function testTransferActionFormNotValid()
    {
        $mockForm = m::mock()
            ->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->getMock();

        $this->sut
            ->shouldReceive('getVehicleTransferForm')
            ->andReturn($mockForm)
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->shouldReceive('getPost')
                ->andReturn([])
                ->getMock()
            )
            ->shouldReceive('renderForm')
            ->with($mockForm)
            ->andReturn('render');

        $this->assertEquals('render', $this->sut->transferAction());
    }

    /**
     * @group abstractVehiclesController
     */
    public function testTransferActionFormValidResponseFailed()
    {
        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $id = 3;
        $mockForm = m::mock()
            ->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn('data')
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('licence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn($targetLicenceId)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService(
            'Lva\TransferVehicles',
            m::mock('\Common\BusinessService\BusinessServiceInterface')
            ->shouldReceive('process')
            ->with(
                [
                    'data' => 'data',
                    'sourceLicenceId' => $sourceLicenceId,
                    'targetLicenceId' => $targetLicenceId,
                    'id' => $id
                ]
            )
            ->andReturn(
                m::mock()
                ->shouldReceive('isOk')
                ->andReturn(false)
                ->once()
                ->shouldReceive('getMessage')
                ->andReturn('message')
                ->once()
                ->getMock()
            )
            ->getMock()
        );
        $this->sm->setService('BusinessServiceManager', $bsm);

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
            ->shouldReceive('addErrorMessage')
            ->with('message')
            ->getMock()
        );

        $this->sut
            ->shouldReceive('getVehicleTransferForm')
            ->andReturn($mockForm)
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->shouldReceive('getPost')
                ->andReturn([])
                ->getMock()
            )
            ->shouldReceive('getLicenceId')
            ->andReturn($sourceLicenceId)
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromRoute')
                ->with('child_id')
                ->andReturn($id)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('renderForm')
            ->with($mockForm)
            ->andReturn('render');

        $this->assertEquals('render', $this->sut->transferAction());
    }

    /**
     * @group abstractVehiclesController
     */
    public function testTransferActionFormValidResponseOk()
    {
        $sourceLicenceId = 1;
        $targetLicenceId = 2;
        $id = 3;
        $mockForm = m::mock()
            ->shouldReceive('setData')
            ->with([])
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn('data')
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('licence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn($targetLicenceId)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService(
            'Lva\TransferVehicles',
            m::mock('\Common\BusinessService\BusinessServiceInterface')
            ->shouldReceive('process')
            ->with(
                [
                    'data' => 'data',
                    'sourceLicenceId' => $sourceLicenceId,
                    'targetLicenceId' => $targetLicenceId,
                    'id' => $id
                ]
            )
            ->andReturn(
                m::mock()
                ->shouldReceive('isOk')
                ->andReturn(true)
                ->once()
                ->getMock()
            )
            ->getMock()
        );
        $this->sm->setService('BusinessServiceManager', $bsm);

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
            ->shouldReceive('addSuccessMessage')
            ->with('licence.vehicles_transfer.form.vehicles_transfered')
            ->getMock()
        );

        $this->sut
            ->shouldReceive('getVehicleTransferForm')
            ->andReturn($mockForm)
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->shouldReceive('getPost')
                ->andReturn([])
                ->getMock()
            )
            ->shouldReceive('getLicenceId')
            ->andReturn($sourceLicenceId)
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromRoute')
                ->with('child_id')
                ->andReturn($id)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('redirect')
            ->andReturn(
                m::mock('Zend\Http\Redirect')
                ->shouldReceive('toRouteAjax')
                ->with(null, ['licence' => $sourceLicenceId])
                ->andReturnSelf()
                ->once()
                ->getMock()
            )
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('licence')
            ->once()
            ->shouldReceive('getIdentifier')
            ->andReturn($sourceLicenceId)
            ->once();

        $this->assertInstanceOf('Zend\Http\Redirect', $this->sut->transferAction());
    }
}
