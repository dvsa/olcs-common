<?php

/**
 * Address Change Task Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use PHPUnit_Framework_TestCase;
use Common\BusinessService\Service\Lva\AddressesChangeTask;
use Common\BusinessService\Response;

/**
 * Address Change Task Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddressesChangeTaskTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

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
