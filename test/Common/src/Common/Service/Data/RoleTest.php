<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\Role;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class RoleService
 * Provides list options for user types
 *
 * @package Olcs\Service
 */
class RoleTest extends MockeryTestCase
{
    public function testFetchListOptions($context = null, $useGroups = false)
    {
        $roleData = [
            'Results' => [
                'role1' => ['id' => 1, 'description' => 'role1desc'],
                'role2' => ['id' => 2, 'description' => 'role2desc']
            ]
        ];

        $expected = [
            1 => 'role1desc',
            2 => 'role2desc'
        ];

        $mockRestClient = $this->getMock('\Common\Util\RestClient', [], [], '', false);
        $mockRestClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo(''), $this->isType('array'))
            ->willReturn($roleData);

        $sut = new Role();
        $sut->setRestClient($mockRestClient);

        $results = $sut->fetchListOptions(null);

        //test data is cached
        $this->assertEquals($expected, $results);
    }
}
