<?php

namespace CommonTest\Filter\Publication\Builder;

use Common\Filter\Publication\Builder\PublicationBuilderAbstractFactory;
use Mockery as m;

/**
 * Class PublicationBuilderAbstractFactoryTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationBuilderAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group publicationFilter
     */
    public function testCreateServiceWithName()
    {
        $sut = new PublicationBuilderAbstractFactory();

        $config = [
            'publications' => [
                'filters' => [
                    'filter1' => 'filter1',
                    'filter2' => 'filter2'
                ]
            ]
        ];

        $filter = m::mock('\Zend\Filter\FilterInterface');
        $filter->shouldReceive('setServiceLocator');

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockServiceManager->shouldReceive('get')->with('Config')->andReturn($config);
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')->with('filter1')->andReturn($filter);
        $mockServiceManager->shouldReceive('get')->with('filter2')->andReturn($filter);

        $this->assertInstanceOf(
            '\Zend\Filter\FilterChain',
            $sut->createServiceWithName($mockServiceManager, '', 'filters')
        );
    }

    /**
     * @param string $requestedName
     * @param bool $expectedValue
     *
     * @dataProvider canCreateServiceProvider
     * @group publicationFilter
     */
    public function testCanCreateServiceWithName($requestedName, $expectedValue)
    {
        $sut = new PublicationBuilderAbstractFactory();

        $config = [
            'publications' => [
                'filters' => [
                    'filter1' => 'filter1',
                    'filter2' => 'filter2'
                ]
            ]
        ];

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockServiceManager->shouldReceive('get')->with('Config')->andReturn($config);

        $this->assertEquals($expectedValue, $sut->canCreateServiceWithName($mockServiceManager, '', $requestedName));
    }

    public function canCreateServiceProvider()
    {
        return [
            ['filters', true],
            ['nofilters', false]
        ];
    }
}
