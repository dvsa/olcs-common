<?php

/**
 * AccessCorrespondenceTest.php
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;

use Common\BusinessService\Response;
use Common\BusinessService\Service\Lva\AccessCorrespondence;
use CommonTest\Bootstrap;

/**
 * Class AccessCorrespondenceTest
 *
 * @package CommonTest\BusinessService\Service\Lva
 */
class AccessCorrespondenceTest extends \PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new AccessCorrespondence();
        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = array(
            'id' => 1,
            'version' => 1
        );

        $correspondenceInboxMock = m::mock()
            ->shouldReceive('update')
            ->with(
                $params['id'],
                array(
                    'accessed' => 'Y',
                    'version' => 1
                )
            )
            ->getMock();

        $this->sm->setService('Entity\CorrespondenceInbox', $correspondenceInboxMock);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function testProcessFails()
    {
        $params = array();

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
    }
}
