<?php

/**
 * Vrm Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Section\VehicleSafety\Vehicle\Formatter;

use Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\Url;
use Laminas\View\HelperPluginManager;

/**
 * Vrm Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VrmTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group VrmFormatter
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $output = Vrm::format($data, $column, $this->getMockedServiceManager());

        $this->assertEquals($expected, $output);
    }

    /**
     * Get the mocked service manager
     *
     * @NOTE This essentially just mocks the url invokeable to return a json_encoded array of params so we can assert
     * what the url would be built with
     *
     * @return object
     */
    private function getMockedServiceManager()
    {
        $urlMock = $this->createPartialMock(Url::class, array('__invoke'));
        $urlMock->expects($this->once())
            ->method('__invoke')
            ->will(
                $this->returnCallback(
                    function ($route, $params, $args, $routeMatch) {
                        return json_encode($params);
                    }
                )
            );

        $mockViewHelper = $this->createPartialMock(HelperPluginManager::class, array('get'));
        $mockViewHelper->expects($this->once())
            ->method('get')
            ->with('url')
            ->will($this->returnValue($urlMock));

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->once())
            ->method('get')
            ->with('viewhelpermanager')
            ->will($this->returnValue($mockViewHelper));

        return $sm;
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                array(
                    'id' => 2,
                    'vrm' => 'ABC123'
                ),
                array(),
                '<a href="{"child_id":2,"action":"edit"}">ABC123</a>'
            ),
            array(
                array(
                    'id' => 2,
                    'vrm' => 'ABC123'
                ),
                array(
                    'action-type' => 'large'
                ),
                '<a href="{"child_id":2,"action":"large-edit"}">ABC123</a>'
            )
        );
    }
}
