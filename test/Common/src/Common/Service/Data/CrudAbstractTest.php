<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\CrudAbstract;
use Mockery as m;
use PHPUnit_Framework_TestCase;

/**
 * Class CrudAbstractTest Test
 * @package CommonTest\Service\Data
 */
class CrudAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Returns a usable instance of the abstract class.
     *
     * @param array $methods
     * @return \Common\Service\Data\CrudAbstract
     */
    public function getSut(array $methods = null)
    {
        return $this->getMock('Common\Service\Data\CrudAbstract', $methods);
    }

    /**
     * Gives us a mocked rest client.
     *
     * @param string $method Name of the method.
     * @return \Common\Util\RestClient
     */
    public function getMockRestClient($method)
    {
        return $this->getMock('Common\Util\RestClient', [$method], array(), '', false, false);
    }

    /**
     * Basic test to ensure we're tesing the right class.
     */
    public function testGetServiceName()
    {
        $this->assertEquals('', $this->getSut()->getServiceName());
    }

    /**
     * @dataProvider dataProviderSave
     * @param array $data
     * @param string $method
     */
    public function testCreateUpdate($data, $method)
    {
        $rest = $this->getMockRestClient($method);
        $rest->expects($this->once())->method($method)->with('', $data)->will($this->returnSelf());

        $sut = $this->getSut(null);
        $sut->setRestClient($rest);

        $this->assertSame($rest, $sut->{$method}(array('data' => json_encode($data))));
    }

    public function dataProviderSave()
    {
        return array(
            array(['id' => '1', 'name' => 'one'], 'update'),
            array(['id' => '', 'name' => 'two'], 'create'),
        );
    }
}
