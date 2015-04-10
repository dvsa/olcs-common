<?php

/**
 * Person Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\Person;
use Common\BusinessService\Response;

/**
 * Person Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PersonTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new Person();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessUpdate()
    {
        $params = [
            'data' => ['id' => 111, 'foo' => 'bar']
        ];

        // Mocks
        $mockPerson = m::mock();
        $this->sm->setService('Entity\Person', $mockPerson);

        // Expectations
        $mockPerson->shouldReceive('save')
            ->with(['id' => 111, 'foo' => 'bar']);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(111, $response->getData()['id']);
    }

    public function testProcessAdd()
    {
        $params = [
            'data' => ['foo' => 'bar']
        ];

        // Mocks
        $mockPerson = m::mock();
        $this->sm->setService('Entity\Person', $mockPerson);

        // Expectations
        $mockPerson->shouldReceive('save')
            ->with(['foo' => 'bar'])
            ->andReturn(['id' => 111]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(111, $response->getData()['id']);
    }
}
