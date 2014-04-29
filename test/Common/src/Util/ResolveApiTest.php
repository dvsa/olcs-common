<?php
/**
 * Test Api resolver
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace CommonTest\Controller;
use Common\Util\ResolveApi;
/**
 * @group form
 */
class ResolveApiTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->resolveApi = $this->getMock('\Common\Util\ResolveApi',
            array('getFullApiPath'),
            array(array('endpoints' =>  array(
                        'payment' => 'http://olcspayment.dev/api/',
                        'backend' => 'http://olcs-backend/',
                    ),
                     'VosaCase' => array(
                        'baseUrl' => 'http://olcs-backend/',
                        'path' => 'vosa-case',
                    )
                )
            )
        );
    }

    public function testMappedApiPath()
    {
        $returned = $this->resolveApi->getClient('VosaCase');
        $this->assertTrue(get_class($returned) === 'Common\Util\RestClient');
    }
    
    public function testUnMappedApiPathWithEndpoint()
    {
        $returned = $this->resolveApi->getClient('backend\VosaCase');
        $this->assertTrue(get_class($returned) === 'Common\Util\RestClient');
    }
    
    /**
     * Test for a form config that does not exist
     * @expectedException Exception
     */
    public function testUnMappedApiPathWithInvalidEndpoint()
    {
        $this->resolveApi->getClient('blah\VosaCase');
    }
    
    public function testUnMappedApiPathWithoutEndpoint()
    {
        $returned = $this->resolveApi->getClient('LicenceCategory');
        $this->assertTrue(get_class($returned) === 'Common\Util\RestClient');
    }

}
