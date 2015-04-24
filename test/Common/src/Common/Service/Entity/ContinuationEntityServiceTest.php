<?php

/**
 * Continuation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ContinuationEntityService;

/**
 * Continuation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ContinuationEntityService();

        parent::setUp();
    }

    public function testFindNoResults()
    {
        $criteria = [
            'foo' => 'bar'
        ];

        $this->expectOneRestCall('Continuation', 'GET', ['foo' => 'bar', 'limit' => 1])
            ->will($this->returnValue(['Results' => []]));

        $this->assertNull($this->sut->find($criteria));
    }

    public function testFind()
    {
        $criteria = [
            'foo' => 'bar'
        ];

        $this->expectOneRestCall('Continuation', 'GET', ['foo' => 'bar', 'limit' => 1])
            ->will($this->returnValue(['Results' => [['foo' => 'bar']]]));

        $this->assertEquals(['foo' => 'bar'], $this->sut->find($criteria));
    }
}
