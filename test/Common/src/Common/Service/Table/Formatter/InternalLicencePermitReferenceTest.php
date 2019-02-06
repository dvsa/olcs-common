<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\InternalLicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * InternalLicencePermitReference test
 */
class InternalLicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/permits/application', ['action' => 'edit', 'permitid' => 3, 'licence' => 200])
            ->andReturn('http://selfserve/permits/application-overview/3');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);

        $sut = new InternalLicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            [
                [
                    'id' => 3,
                    'licenceId' => 200,
                    'applicationRef' => 'ECMT>1234567',
                ],
                '<a href="http://selfserve/permits/application-overview/3">ECMT&gt;1234567</a>'
            ],
        ];
    }
}
