<?php

/**
 * Generic Business Type Adapter tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\GenericBusinessTypeAdapter;

/**
 * Generic Business Type Adapter tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GenericBusinessTypeAdapterTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new GenericBusinessTypeAdapter();
    }

    public function testAlterFormIsNoOp()
    {
        $this->assertNull($this->sut->alterFormForOrganisation(m::mock('Zend\Form\Form'), 123));
    }
}
