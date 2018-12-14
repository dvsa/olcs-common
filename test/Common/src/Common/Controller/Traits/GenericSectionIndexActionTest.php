<?php

/**
 * Generic Section Index Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

/**
 * Generic Section Index Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericSectionIndexActionTest extends \PHPUnit\Framework\TestCase
{
    private $sut;

    protected function setUp()
    {
        $this->sut = $this->getMockForTrait(
            '\Common\Controller\Traits\GenericSectionIndexAction',
            array(),
            '',
            true,
            true,
            true,
            array('goToFirstSubSection')
        );
    }

    /**
     * @group controller_traits
     * @group generic_section_controller_traits
     */
    public function testIndexAction()
    {
        $this->sut->expects($this->once())
            ->method('goToFirstSubSection');

        $this->sut->indexAction();
    }
}
