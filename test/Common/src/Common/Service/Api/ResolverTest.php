<?php


namespace CommonTest\Service\Api;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Service\Api\Resolver;

/**
 * Class ResolverTest
 * @package CommonTest\Service\Api
 */
class ResolverTest extends TestCase
{
    public function testGetClient()
    {
        $mockService = new \StdClass();

        $sut = new Resolver();
        $sut->setService('Olcs\RestService\Backend\Tasks', $mockService);

        $this->assertSame($mockService, $sut->getClient('Backend\Tasks'));
    }

    public function testValidatePlugin()
    {
        $sut = new Resolver();
        $this->assertNull($sut->validatePlugin(null));
    }
}
