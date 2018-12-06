<?php

/**
 * Generic Add Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

use PHPUnit_Framework_TestCase;

/**
 * Generic Add Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericAddActionTest extends PHPUnit_Framework_TestCase
{
    private $sut;

    protected function setUp()
    {
        $this->sut = $this->getMockForTrait(
            '\Common\Controller\Traits\GenericAddAction',
            array(),
            '',
            true,
            true,
            true,
            array('renderSection')
        );
    }

    /**
     * @group controller_traits
     * @group generic_section_controller_traits
     */
    public function testIndexAction()
    {
        $this->sut->expects($this->once())
            ->method('renderSection');

        $this->sut->addAction();
    }
}
