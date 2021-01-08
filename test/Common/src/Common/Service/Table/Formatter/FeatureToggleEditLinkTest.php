<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\FeatureToggleEditLink;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeatureToggleEditLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class FeatureToggleEditLinkTest extends MockeryTestCase
{
    public function testFormat()
    {
        $sut = new FeatureToggleEditLink();

        $id = 123;
        $friendlyName = 'friendly name';
        $url = 'http://url.com';

        $data = [
            'id' => $id,
            'friendlyName' => $friendlyName
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->once()
            ->with(FeatureToggleEditLink::URL_ROUTE, ['id' => $id, 'action' => FeatureToggleEditLink::URL_ACTION])
            ->andReturn($url);

        $expected = sprintf(FeatureToggleEditLink::LINK_PATTERN, $url, $friendlyName);

        $this->assertEquals($expected, $sut->format($data, [], $sm));
    }
}
