<?php

/**
 * Send Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\SendTransportManagerApplication;

/**
 * Send Transport Manager Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendTransportManagerApplicationTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager');

        $this->sut = new SendTransportManagerApplication();
    }
}
