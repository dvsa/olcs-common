<?php

/**
 * OrganisationLinkTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\OrganisationLink;

/**
 * Class OrganisationLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OrganisationLinkTest extends TestCase
{
    public function testFormat()
    {
        $data = [
            'organisation' => [
                'id' => 69,
                'name' => 'Foobar Ltd.'
            ],
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'operator/business-details',
                        [
                            'organisation' => $data['organisation']['id'],
                        ]
                    )
                    ->andReturn('ORGANISATION_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            '<a class="govuk-link" href="ORGANISATION_URL">Foobar Ltd.</a>',
            OrganisationLink::format($data, [], $sm)
        );
    }
}
