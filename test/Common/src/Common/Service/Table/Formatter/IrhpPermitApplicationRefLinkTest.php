<?php

/**
 * IrhpPermitApplicationRefLink Test
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IrhpPermitApplicationRefLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpPermitApplicationRefLinkTest extends MockeryTestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->with(
                'licence/irhp-application',
                [
                    'action' => 'index',
                    'licence' => 100
                ]
            )
            ->times(isset($data) ? 1 : 0)
            ->andReturn('url');

        $this->assertEquals($expected, IrhpPermitApplicationRefLink::format($data, [], $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'with value' => [
                [
                    'irhpPermitApplication' => [
                        'relatedApplication' => [
                            'applicationRef' => 'app ref>',
                            'licence' => [
                                'id' => 100
                            ]
                        ]
                    ]
                ],
                '<a class="govuk-link" href="url">app ref&gt;</a>',
            ],
            'empty value' => [
                null,
                ''
            ]
        ];
    }
}
