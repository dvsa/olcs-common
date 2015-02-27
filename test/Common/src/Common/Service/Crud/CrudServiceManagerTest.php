<?php

/**
 * Crud Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Crud;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Crud\CrudServiceManager;
use CommonTest\Bootstrap;

/**
 * Crud Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new CrudServiceManager();

        $this->sm = Bootstrap::getServiceManager();
    }
}
