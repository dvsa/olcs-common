<?php

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use PHPUnit_Framework_TestCase;
use Common\BusinessService\Service\Lva\PsvVehicles;
use Common\BusinessService\Response;

/**
 * Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehiclesTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new PsvVehicles();
    }

    public function testProcess()
    {
        $response = $this->sut->process([]);

        $this->assertEquals(Response::TYPE_NO_OP, $response->getType());
        $this->assertEquals([], $response->getData());
    }
}
