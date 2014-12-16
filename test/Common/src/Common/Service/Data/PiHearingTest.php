<?php

namespace OlcsTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\PiHearing;
use Mockery as m;

/**
 * Class PiHearing Test
 * @package CommonTest\Service
 */
class PiHearingTest extends MockeryTestCase
{
    /**
     * tests fetch list
     */
    public function testFetchList()
    {
        $data = [
            'Results' => [
                0 => [
                    'id' => 1
                ]
            ]
        ];

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')->once()->andReturn($data);

        $sut = new PiHearing();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals($data, $sut->fetchList());
    }
}
