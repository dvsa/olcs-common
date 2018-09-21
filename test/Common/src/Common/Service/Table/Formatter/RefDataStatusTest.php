<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\RefDataStatus;
use Common\View\Helper\Status as StatusHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RefDataStatus test
 */
class RefDataStatusTest extends MockeryTestCase
{
    /**
     * tests formatting of ref data statuses
     */
    public function testFormat()
    {
        $outputStatus = 'output status';
        $description = 'start description';
        $columnId = 'column id';

        $columnName = 'column name';

        $data = [
            $columnName => [
                'id' => $columnId,
                'description' => 'start description',
            ],
        ];

        $column = [
            'name' => $columnName
        ];

        $statusInput = [
            'id' => $columnId,
            'description' => $description
        ];

        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);

        $statusHelper = m::mock(StatusHelper::class);
        $statusHelper->shouldReceive('__invoke')
            ->once()
            ->with($statusInput)
            ->andReturn($outputStatus);

        //this is our status helper
        $mockSm->shouldReceive('get->get')
            ->once()
            ->andReturn($statusHelper);

        $mockSm
            ->shouldReceive('get->translate')
            ->andReturn($description);

        $this->assertEquals($outputStatus, RefDataStatus::format($data, $column, $mockSm));
    }
}
