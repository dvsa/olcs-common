<?php

/**
 * Variation Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\VariationCompletionEntityService;

/**
 * Variation Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationCompletionEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new VariationCompletionEntityService();

        parent::setUp();
    }

    public function testUpdateCompletionStatuses()
    {
        // Params
        $appId = 3;
        $stubbedStatuses = [
            'Count' => 1,
            'Results' => [
                [
                    'id' => 3,
                    'version' => 2,
                    'fooBarStatus' => 0,
                    'barStatus' => 0
                ]
            ]
        ];
        $statuses = [
            'foo_bar' => 1,
            'bar' => 2
        ];
        $expectedData = [
            'id' => 3,
            'version' => 2,
            'fooBarStatus' => 1,
            'barStatus' => 2
        ];

        // Mocks
        $mockStringHelper = m::mock();
        $this->sm->setService('Helper\String', $mockStringHelper);

        // Expectations
        $mockStringHelper->shouldReceive('underscoreToCamel')
            ->with('foo_bar')
            ->andReturn('fooBar')
            ->shouldReceive('underscoreToCamel')
            ->with('bar')
            ->andReturn('bar');

        $this->expectedRestCallInOrder('ApplicationCompletion', 'GET', ['application' => $appId])
            ->willReturn($stubbedStatuses);

        $this->expectedRestCallInOrder('ApplicationCompletion', 'PUT', $expectedData);

        $this->sut->updateCompletionStatuses($appId, $statuses);
    }

    public function testGetCompletionStatuses()
    {
        // Params
        $appId = 3;
        $stubbedStatuses = [
            'Count' => 1,
            'Results' => [
                [
                    'id' => 3,
                    'version' => 2,
                    'fooBarStatus' => 2,
                    'barStatus' => 1
                ]
            ]
        ];

        $expected = [
            'foo_bar' => 2,
            'bar' => 1
        ];

        // Mocks
        $mockStringHelper = m::mock();
        $this->sm->setService('Helper\String', $mockStringHelper);

        // Expectations
        $mockStringHelper->shouldReceive('camelToUnderscore')
            ->with('fooBar')
            ->andReturn('foo_bar')
            ->shouldReceive('camelToUnderscore')
            ->with('bar')
            ->andReturn('bar');

        $this->expectOneRestCall('ApplicationCompletion', 'GET', ['application' => $appId])
            ->willReturn($stubbedStatuses);

        $this->assertEquals($expected, $this->sut->getCompletionStatuses($appId));
    }
}
