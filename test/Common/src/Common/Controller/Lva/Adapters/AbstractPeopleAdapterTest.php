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
    /**
     * @dataProvider dataProvider
     */
    public function testAlterFormForOrganisation($type, $expected)
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
            ->with('add', ['label' => $expected])
            ->once()
            ->getMock();

        $sut->shouldReceive('getOrganisationType')
            ->andReturn($type)
            ->twice()
            ->getMock();

        $sut->alterFormForOrganisation(m::mock(Form::class), $mockTable);
    }

    public function dataProvider()
    {
        return [
            'ltd' => [
                \Common\RefData::ORG_TYPE_RC,
                'lva.section.title.add_director'
            ],
            'llp' => [
                \Common\RefData::ORG_TYPE_LLP,
                'lva.section.title.add_partner'
            ],
            'partnership' => [
                \Common\RefData::ORG_TYPE_PARTNERSHIP,
                'lva.section.title.add_partner'
            ],
            'other' => [
                \Common\RefData::ORG_TYPE_OTHER,
                'lva.section.title.add_person'
            ]
        ];
    }
}
