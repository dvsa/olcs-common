<?php

/**
 * Licence Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentres;
use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Table\TableBuilder;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Http\Request;
use Common\Service\Helper\FormHelperService;

/**
 * Licence Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentresTest extends MockeryTestCase
{
    protected $form;

    /**
     * @var LicenceOperatingCentres
     */
    protected $sut;

    protected $mockFormHelper;

    protected $tableBuilder;

    public function setUp(): void
    {
        $this->tableBuilder = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Table', $this->tableBuilder);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->form = m::mock(Form::class);

        $lvaLicence = m::mock(FormServiceInterface::class);
        $lvaLicence->shouldReceive('alterForm')
            ->once()
            ->with($this->form);

        $fsm->setService('lva-licence', $lvaLicence);

        $this->mockFormHelper = m::mock(FormHelperService::class);
        $this->mockFormHelper->shouldReceive('createForm')
            ->once()
            ->with('Lva\OperatingCentres')
            ->andReturn($this->form);

        $this->sut = new LicenceOperatingCentres();
        $this->sut->setFormHelper($this->mockFormHelper);
        $this->sut->setFormServiceLocator($fsm);
    }

    public function testGetForm()
    {
        $params = [
            'operatingCentres' => [],
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
        ];

        $this->mockPopulateFormTable([]);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea');

        $totCommunityLicences = m::mock(Element::class);

        $data = m::mock();
        $data->shouldReceive('has')
            ->with('totCommunityLicences')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('totCommunityLicences')
            ->andReturn($totCommunityLicences);

        $this->mockFormHelper->shouldReceive('disableElement')
            ->once()
            ->with($this->form, 'data->totCommunityLicences');

        $this->mockFormHelper->shouldReceive('lockElement')
            ->once()
            ->with($totCommunityLicences, 'community-licence-changes-contact-office');

        $this->form->shouldReceive('get')
            ->with('data')
            ->andReturn($data);

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    protected function mockPopulateFormTable($data)
    {
        $rows = [
            ['noOfLgvVehiclesRequired' => 1]
        ];

        $table = m::mock(TableBuilder::class);
        $table->shouldReceive('getRows')
            ->andReturn($rows);

        $tableElement = m::mock(Fieldset::class);
        $tableElement->shouldReceive('get')
            ->with('table')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getTable')
            ->withNoArgs()
            ->andReturn($table);

        $this->form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableElement);

        $this->tableBuilder->shouldReceive('prepareTable')
            ->with('lva-operating-centres', $data, [])
            ->andReturn($table);

        $this->mockFormHelper->shouldReceive('populateFormTable')
            ->with($tableElement, $table);

        return $tableElement;
    }
}
