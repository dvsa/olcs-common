<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\UserTypesListDataService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class UserTypesListDataService
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UserTypesListDataServiceTest extends MockeryTestCase
{

    public function testCreateService()
    {
        $sut = new UserTypesListDataService();

        $mockRefDataService = m::mock('\Common\Service\Data\RefData');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('\Common\Service\Data\RefData')->andReturn($mockRefDataService);

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\Data\UserTypesListDataService', $service);
    }

    public function testFetchListOptions()
    {
        $sut = new UserTypesListDataService();

        $options = $sut->fetchListOptions();
        $this->assertArrayHasKey('internal', $options);
    }
}
