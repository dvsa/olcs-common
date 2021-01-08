<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

/**
 * @covers \Common\Data\Object\Search\OperatingCentreSelfserve
 */
class OperatingCentreSelfserveTest extends SearchAbstractTest
{
    protected $class = \Common\Data\Object\Search\OperatingCentreSelfserve::class;

    public function testLicNoFormatter()
    {
        $data = [
            'licId' => 'unit_LicId',
            'licNo' => 'unit_LicNo',
            'licStatusDesc' => 'unit_LicStatusDesc',
        ];

        $mockTranslator = m::mock(\Laminas\I18n\Translator\Translator::class)
            ->shouldReceive('translate')
            ->once()
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSL_' . $key;
                }
            )
            ->getMock();

        $mockSm = m::mock(\Laminas\Di\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('translator')->andReturn($mockTranslator)
            ->getMock();

        $col = $this->sut->getColumns()[0];

        static::assertEquals(
            '<a href="/view-details/licence/unit_LicId">unit_LicNo</a><br/>_TRNSL_unit_LicStatusDesc',
            $col['formatter']($data, null, $mockSm)
        );
    }
}
