<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;
use Common\Service\Entity\VehicleEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Test Abstract Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesPsvControllerTest extends AbstractLvaControllerTestCase
{
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesPsvController');

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');
        $this->sut->setAdapter($this->adapter);

        $this->mockService('Script', 'loadFiles')->with(['lva-crud', 'vehicle-psv']);

        // stub the mapping between type and psv type that is now in entity service
        $map = [
            'small'  => 'vhl_t_a',
            'medium' => 'vhl_t_b',
            'large'  => 'vhl_t_c',
        ];
        $this->mockEntity('Vehicle', 'getTypeMap')->andReturn($map);
        $this->mockEntity('Vehicle', 'getPsvTypeFromType')->andReturnUsing(
            function ($type) use ($map) {
                return isset($map[$type]) ? $map[$type] : null;
            }
        );
    }

    public function testSmallAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'small')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallAddAction());
    }

    public function testSmallEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'small')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallEditAction());
    }

    public function testSmallDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallDeleteAction());
    }

    public function testMediumAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'medium')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumAddAction());
    }

    public function testMediumEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'medium')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumEditAction());
    }

    public function testMediumDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumDeleteAction());
    }

    public function testLargeAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'large')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeAddAction());
    }

    public function testLargeEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'large')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeEditAction());
    }

    public function testLargeDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeDeleteAction());
    }

    /**
     * Test that tables are not removed when there is no vehicle authority
     * but previously added vehicles
     *
     * @see https://jira.i-env.net/browse/OLCS-7590
     */
    public function testAlterFormKeepsTablesWithVehiclesWhenNoAuthority()
    {
        $mockForm = $this->createMockForm('Lva\PsvVehicles');

        $this->mockRowField($mockForm, 'small', 2);
        $this->mockRowField($mockForm, 'medium', 3);
        $this->mockRowField($mockForm, 'large', 4);

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([2, 3, 4])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('hasEnteredReg')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getValidatorChain')
                        ->andReturn(
                            m::mock()
                            ->shouldReceive('attach')
                            ->with($mockValidator)
                            ->getMock()
                        )
                        ->getMock()
                    )
                    ->getMock()
                )
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($mockForm, 'data->notice');

        $this->sut->shouldReceive('getTypeOfLicenceData')->andReturn(
            [
                'version'     => 1,
                'niFlag'      => 'N',
                'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'goodsOrPsv'  => LicenceEntityService::LICENCE_CATEGORY_PSV,
            ]
        );

        $id = 69;
        $data = [
            'id' => $id,
            'totAuthVehicles'       => 5,
            'totAuthSmallVehicles'  => 2,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles'  => 0,
        ];

        $this->adapter
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_SMALL)
                ->andReturn(2)
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_MEDIUM)
                ->andReturn(2)
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_LARGE)
                ->andReturn(1)
            ->shouldReceive('warnIfAuthorityExceeded');

        $this->assertSame($mockForm, $this->sut->alterForm($mockForm, $data));
    }

    protected function mockRowField($form, $name, $value)
    {
        $form->shouldReceive('get')
            ->with($name)
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('rows')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getValue')
                            ->andReturn($value)
                            ->getMock()
                    )
                    ->getMock()
            );
    }

    /**
     * @dataProvider getDeleteMessageProvider
     */
    public function testGetDeleteMessage($params, $totalVehicles, $licence, $expected)
    {
        $licenceId = 1;

        // Set by Provider.

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn($params);

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn($licenceId);

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('getOverview')
                ->with($licenceId)
                ->andReturn($licence)
                ->getMock()
        );

        $this->sut->shouldReceive('getTotalNumberOfVehicles')
            ->andReturn($totalVehicles);

        $this->assertEquals($expected, $this->sut->getDeleteMessage());
    }

    public function getDeleteMessageProvider()
    {
        return array(
            array(
                '1',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    )
                ),
                'deleting.all.vehicles.message'
            ),
            array(
                '1,2',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    )
                ),
                'delete.confirmation.text'
            ),

            array(
                '1,2',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'NotInAcceptedArray'
                    )
                ),
                'delete.confirmation.text'
            )
        );
    }

    public function testSmallTransferAction()
    {
        $this->sut
            ->shouldReceive('transferVehicles')
            ->once()
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallTransferAction());
    }

    public function testMediumlTransferAction()
    {
        $this->sut
            ->shouldReceive('transferVehicles')
            ->once()
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumTransferAction());
    }

    public function testLargelTransferAction()
    {
        $this->sut
            ->shouldReceive('transferVehicles')
            ->once()
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeTransferAction());
    }

    public function testRenderForm()
    {
        $this->sut
            ->shouldReceive('render')
            ->with('vehicles_psv', 'form')
            ->once()
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->renderForm('form'));
    }

    /**
     * @group avt1
     */
    public function testGetIndexActionWithSmallVehicles()
    {
        $this->mockRender();

        $entityData = [
            'version' => 1,
            'hasEnteredReg' => 'Y'
        ];

        $form = $this->createMockForm('Lva\PsvVehicles');

        $formTable = m::mock('Zend\Form\Fieldset');
        $table = m::mock('Common\Service\Table\TableBuilder');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => $entityData
                ]
            )
            ->andReturnSelf()
            ->shouldReceive('has')
            ->with('small')
            ->andReturn(true)
            ->shouldReceive('has')
            ->andReturn(false)
            ->shouldReceive('get')
            ->with('small')
            ->andReturn($formTable);

        $this->adapter->shouldReceive('getVehiclesData')
            ->with(1)
            ->andReturn([])
            ->shouldReceive('warnIfAuthorityExceeded')
            ->shouldReceive('alterVehcileTable')
            ->once();

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(1)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with(1)
                ->andReturn($entityData)
                ->getMock()
            )
            // alterForm shouldn't really be mocked, but a) it's public already (!)
            // and b) it's heavily tested standalone earlier in this file
            ->shouldReceive('alterForm')
            ->andReturn($form);

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-small', [])
            ->andReturn($table)
            ->getMock()
        );

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable')
            ->with($formTable, $table, 'small');

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }
}
