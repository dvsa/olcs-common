<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\TransportManager;
use Common\Data\Object\Publication;
use Mockery as m;
use Common\Exception\ResourceNotFoundException;

/**
 * Class TransportManagerTest
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TransportManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group publicationFilter
     *
     */
    public function testFilter()
    {
        $pi = 1;
        $personData = [
            'title' => 'Mr',
            'forename' => 'John',
            'familyName' => 'Smith',
        ];

        $publicationData = [
            'pi' => $pi,
            'case' => [
                'id' => 84,
                'transportManager' => [
                    'id' => 4
                ]
            ]
        ];

        $mockTmData = [
            'id' => 4,
            'workCd' => [
                'person' => $personData
            ]
        ];
        $mockTmService = m::mock('\Common\Service\Data\TransportManager');
        $mockTmService->shouldReceive('fetchTmData')
            ->with($publicationData['case']['transportManager']['id'])
            ->andReturn($mockTmData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\TransportManager')->andReturn($mockTmService);

        $expectedOutput = $personData['title'] . ' ' . $personData['forename'] . ' ' . $personData['familyName'];

        $input = new Publication($publicationData);
        $sut = new TransportManager();
        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('transportManagerName'));
    }

    /**
     * @group publicationFilter
     * @expectedException Common\Exception\ResourceNotFoundException
     */
    public function testFilterException()
    {
        $pi = 1;
        $personData = [
            'title' => 'Mr',
            'forename' => 'John',
            'familyName' => 'Smith',
        ];

        $publicationData = [
            'pi' => $pi,
            'case' => [
                'id' => 84,
                'transportManager' => [
                    'id' => 4
                ]
            ]
        ];

        $mockTmData = []; // forces exception
        $mockTmService = m::mock('\Common\Service\Data\TransportManager');
        $mockTmService->shouldReceive('fetchTmData')
            ->with($publicationData['case']['transportManager']['id'])
            ->andReturn($mockTmData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\TransportManager')->andReturn($mockTmService);

        $expectedOutput = $personData['title'] . ' ' . $personData['forename'] . ' ' . $personData['familyName'];

        $input = new Publication($publicationData);
        $sut = new TransportManager();
        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('transportManagerName'));
    }
}
