<?php

/**
 * Internal / Common Application People Adapter Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace OlcsTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationPeopleAdapter;
use Common\Service\Entity\OrganisationEntityService;

/**
 * Internal / Common Application People Adapter Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationPeopleAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->sut = new ApplicationPeopleAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     *
     */
    public function testAlterFormForOrganisationDoesNotAlterFormWithInForceLicences()
    {
        $this->sm->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('hasInForceLicences')
            ->with(123)
            ->andReturn(true)
            ->getMock()
        );

        $form = m::mock('Zend\Form\Form');
        $table = m::mock(Table::class);

        $table->shouldReceive('getAction')->andReturn(['label'=>'']);
        $table->shouldReceive('removeAction');
        $table->shouldReceive('addAction');
        $this->assertNull($this->sut->alterFormForOrganisation($form, $table, 123));
    }

    public function testAlterFormForOrganisationDoesNotAlterFormWithoutInForceLicences()
    {
        $this->sm->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('hasInForceLicences')
            ->with(123)
            ->andReturn(false)
            ->getMock()
        );

        $form = m::mock('Zend\Form\Form');
        $table = m::mock(Table::class);

        $table->shouldReceive('getAction')->andReturn(['label'=>'']);
        $table->shouldReceive('removeAction');
        $table->shouldReceive('addAction');

        $this->assertNull($this->sut->alterFormForOrganisation($form, $table, 123));
    }

    public function testAlterAddOrEditFormForOrganisationDoesNotAlterFormWithInForceLicences()
    {
        $this->sm->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('hasInForceLicences')
            ->with(123)
            ->andReturn(true)
            ->getMock()
        );

        $form = m::mock('Zend\Form\Form');

        $this->assertNull($this->sut->alterAddOrEditFormForOrganisation($form, 123));
    }

    public function testAlterAddOrEditFormForOrganisationDoesNotAlterFormWithoutInForceLicences()
    {
        $this->sm->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('hasInForceLicences')
            ->with(123)
            ->andReturn(false)
            ->getMock()
        );

        $form = m::mock('Zend\Form\Form');

        $this->assertNull($this->sut->alterAddOrEditFormForOrganisation($form, 123));
    }

    public function testCanModifyIsAlwaysTrueInternally()
    {
        $this->assertTrue($this->sut->canModify(123));
    }
}
