<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IssuedPermitLicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * IssuedPermitLicencePermitReference test
 */
class IssuedPermitLicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-permits', ['permitid' => 3, 'licence' => 200])
            ->andReturn('http://selfserve/permits/irhp-permits/3');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);

        $sut = new IssuedPermitLicencePermitReference();
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
                '<a href="http://selfserve/permits/irhp-permits/3">ECMT&gt;1234567</a>'
            ],
        ];
    }
}
