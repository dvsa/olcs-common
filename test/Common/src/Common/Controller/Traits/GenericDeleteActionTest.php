<?php

/**
 * Generic Delete Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

use PHPUnit_Framework_TestCase;

/**
 * Generic Delete Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericDeleteActionTest extends PHPUnit_Framework_TestCase
{
    private $sut;

    protected function setUp()
    {
        $this->sut = $this->getMockForTrait(
            '\Common\Controller\Traits\GenericDeleteAction',
            array(),
            '',
            true,
            true,
            true,
            array('delete')
        );
    }

    /**
     * @group controller_traits
     * @group generic_section_controller_traits
     */
    public function testIndexAction()
    {
        $this->sut->expects($this->once())
            ->method('delete');

        $this->sut->deleteAction();
    }
}
