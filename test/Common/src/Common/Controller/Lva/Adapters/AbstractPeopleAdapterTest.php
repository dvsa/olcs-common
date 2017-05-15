<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\AbstractController;
use Common\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;
use Common\Service\Table\TableBuilder;
use Common\Controller\Lva\Adapters\AbstractPeopleAdapter;

/**
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @covers \Common\Controller\Lva\Adapters\AbstractPeopleAdapter
 */
class AbstractPeopleAdapterTest extends MockeryTestCase
{
    const ID = 9001;

    /** @var  m\MockInterface | AbstractPeopleAdapter */
    private $sut;

    public function setUp()
    {
        $this->sut = m::mock(AbstractPeopleAdapter::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function testLoadPeopleDataLic()
    {
        $this->sut->shouldReceive('loadPeopleDataForLicence')->with(self::ID);

        static::assertTrue($this->sut->loadPeopleData(AbstractController::LVA_LIC, self::ID));
    }

    public function testLoadPeopleDataOth()
    {
        $this->sut->shouldReceive('loadPeopleDataForApplication')->with(self::ID);

        static::assertTrue($this->sut->loadPeopleData(AbstractController::LVA_VAR, self::ID));
        static::assertTrue($this->sut->loadPeopleData(AbstractController::LVA_APP, self::ID));
    }

    /** @dataProvider dpTestHasInforceLicences */
    public function testHasInforceLicences($data, $expect)
    {
        $this->sut->shouldReceive('getOrganisation')->once()->andReturn($data);

        static::assertEquals($expect, $this->sut->hasInforceLicences());
    }

    public function dpTestHasInforceLicences()
    {
        return[
            [
                'data'=> [
                    'hasInforceLicences' => false,
                ],
                'expect'=> false,
            ],
            [
                'data'=> [
                    'hasInforceLicences' => true,
                ],
                'expect'=> true,
            ],
            [
                'data'=> [],
                'expect'=> false,
            ],
        ];
    }

    /** @dataProvider dpTestIsExceptionalOrganisation */
    public function testIsExceptionalOrganisation($type, $expect)
    {
        $this->sut->shouldReceive('getOrganisationType')->once()->andReturn($type);

        static::assertEquals($expect, $this->sut->isExceptionalOrganisation());
    }

    public function dpTestIsExceptionalOrganisation()
    {
        return[
            [
                'type'=> RefData::ORG_TYPE_REGISTERED_COMPANY,
                'expect'=> false,
            ],
            [
                'type'=> RefData::ORG_TYPE_PARTNERSHIP,
                'expect'=> true,
            ],
            [
                'type'=> RefData::ORG_TYPE_SOLE_TRADER,
                'expect'=> true,
            ],
        ];
    }

    /**
     * @dataProvider dpTestIsSoleTrader
     */
    public function testIsSoleTrader($type, $expect)
    {
        $this->sut->shouldReceive('getOrganisationType')->once()->andReturn($type);

        static::assertEquals($expect, $this->sut->isSoleTrader());
    }

    public function dpTestIsSoleTrader()
    {
        return[
            [
                'type'=> RefData::ORG_TYPE_REGISTERED_COMPANY,
                'expect'=> false,
            ],
            [
                'type'=> RefData::ORG_TYPE_SOLE_TRADER,
                'expect'=> true,
            ],
        ];
    }
    /**
     * @dataProvider dpTestAlterFormForOrganisation
     */
    public function testAlterFormForOrganisation($type, $expected)
    {
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

        $this->sut->shouldReceive('getOrganisationType')
            ->andReturn($type)
            ->twice()
            ->getMock();

        $this->sut->alterFormForOrganisation(m::mock(Form::class), $mockTable);
    }

    public function dpTestAlterFormForOrganisation()
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
