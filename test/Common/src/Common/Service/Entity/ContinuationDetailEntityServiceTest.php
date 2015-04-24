<?php

/**
 * Continuation Detail Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ContinuationDetailEntityService;

/**
 * Continuation Detail Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationDetailEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ContinuationDetailEntityService();

        parent::setUp();
    }

    public function testCreateRecords()
    {
        $records = [
            ['foo' => 'bar']
        ];

        $data = [
            ['foo' => 'bar'],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectOneRestCall('ContinuationDetail', 'POST', $data);

        $this->sut->createRecords($records);
    }
}
