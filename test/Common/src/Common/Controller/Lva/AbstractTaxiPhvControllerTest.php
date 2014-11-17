<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Taxi PHV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractTaxiPhvControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTaxiPhvController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\TaxiPhv');

        $form->shouldReceive('setData')
            ->with(
                [
                    'dataTrafficArea' => []
                ]
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(m::mock('\Zend\Form\Fieldset'))
            ->shouldReceive('get')
            ->with('dataTrafficArea')
            // @NOTE: (NP) just trying out this nested anonymous mock
            // style. If you hate it, feel free to flatten out into
            // variables instead
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('trafficArea')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setValueOptions')
                    ->with([1, 2])
                    ->getMock()
                )
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->shouldRemoveElements(
            $form,
            [
                'dataTrafficArea->trafficAreaInfoLabelExists',
                'dataTrafficArea->trafficAreaInfoNameExists',
                'dataTrafficArea->trafficAreaInfoHintExists'
            ]
        );

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(123);

        $this->mockEntity('Licence', 'getTrafficArea')
            ->with(123)
            ->andReturn([]);

        $this->mockEntity('PrivateHireLicence', 'getByLicenceId')
            ->with(123)
            ->andReturn([]);

        $this->mockEntity('TrafficArea', 'getValueOptions')
            ->andReturn([1, 2]);

        $this->mockService('Table', 'prepareTable')
            ->with('lva-taxi-phv', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('taxi_phv', $this->view);
    }

    public function testAddFormPostcodeLookup()
    {
        $postData = [
            'data' => [
                'address' => [
                    'searchPostcode' => [
                        'postcode' => 'ls9 6nf',
                        'search' => 'search'
                    ]
                ]
            ]
        ];
        $this->setPost($postData);

        $form = $this->createMockForm('Lva\TaxiPhv');
        $form->shouldReceive('setData')
                ->with($postData)
                ->andReturn($form);
        // assert that form isn't validated if we're only doing postcode lookup
        // (i.e. processAddressLookupForm returns true, below)
        $form->shouldReceive('isValid')->never();

        $this->sut->shouldReceive('getLicenceForm')
            ->andReturn($form);

        $this->formHelper
            ->shouldReceive('processAddressLookupForm')
            ->with($form, $this->request)
            ->andReturn(true);

        $this->mockRender();
        $this->sut->addAction();

        $this->assertEquals('add_taxi_phv', $this->view);

    }

    public function testAddFormWithAddressPopulated()
    {
        $postData = [
            'data' => [
                'address' => [
                    // don't actually need data in here as form helper is mocked
                ]
            ]
        ];
        $this->setPost($postData);

        $form = $this->createMockForm('Lva\TaxiPhv');
        $form->shouldReceive('setData')
                ->with($postData)
                ->andReturn($form);

        // assert that form *is* validated if not doing postcode lookup
        // (i.e. processAddressLookupForm returns false, below)
        $form->shouldReceive('isValid')
                ->once()
                ->andReturn(false);

        $this->sut->shouldReceive('getLicenceForm')
            ->andReturn($form);

        $this->formHelper
            ->shouldReceive('processAddressLookupForm')
            ->with($form, $this->request)
            ->andReturn(false);


        $this->mockRender();
        $this->sut->addAction();

        $this->assertEquals('add_taxi_phv', $this->view);
    }
}
