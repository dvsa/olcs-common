<?php

namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;
use Common\Service\Table\TableBuilder;
use Common\Controller\Lva\Adapters\AbstractPeopleAdapter;

/**
 * Abstract People Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractPeopleAdapterTest extends MockeryTestCase
{
    public function testAlterFormForOrganisation()
    {
        $sut = m::mock(AbstractPeopleAdapter::class)
            ->makePartial();

        $mockTable = m::mock(TableBuilder::class)
            ->shouldReceive('getAction')
            ->with('add')
            ->andReturn([])
            ->once()
            ->shouldReceive('removeAction')
            ->with('add')
            ->once()
            ->shouldReceive('addAction')
            ->with('add', ['label' => 'lva.section.title.add_person'])
            ->once()
            ->getMock();

        $sut->shouldReceive('isOrganisationLimitedCompany')
            ->andReturn(false)
            ->once()
            ->shouldReceive('isOrganisationLlp')
            ->andReturn(false)
            ->once()
            ->shouldReceive('isOrganisationPartnership')
            ->andReturn(false)
            ->once()
            ->shouldReceive('getOrganisationType')
            ->andReturn(\Common\RefData::ORG_TYPE_OTHER)
            ->twice()
            ->getMock();

        $sut->alterFormForOrganisation(m::mock(Form::class), $mockTable);
    }
}
