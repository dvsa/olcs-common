<?php

/**
 * Address Change Task Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\AddressesChangeTask;
use Common\BusinessService\Response;

/**
 * Address Change Task Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddressChangeTaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sut = new AddressesChangeTask();
    }

    public function testProcessIsNoOp()
    {
        $response = $this->sut->process([]);

        $this->assertEquals(Response::TYPE_NO_OP, $response->getType());
        $this->assertEquals([], $response->getData());
    }
}
