<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
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
    public function testFormat()
    {
        $appId = 4;
        $licenceId = 200;
        $expectedOutput = '<a href="INTERNAL_IRHP_URL">OB1234567/4&gt;</a>'; //escaped as proved by &gt;

        $row = [
            'id' => $appId,
            'licenceId' => $licenceId,
            'applicationRef' => 'OB1234567/4>',
        ];

        $routeParams = [
            'action' => 'edit',
            'irhpAppId' => $appId,
            'licence' => $licenceId
        ];

        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-application/application', $routeParams)
            ->once()
            ->andReturn('INTERNAL_IRHP_URL');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->once()->with('Helper\Url')->andReturn($urlHelper);

        $sut = new InternalLicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }
}
