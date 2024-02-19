<?php

/**
 * Generic Edit Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

/**
 * Generic Edit Action Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericEditActionTest extends \PHPUnit\Framework\TestCase
{
    private $sut;

    protected function setUp(): void
    {
        $this->sut = $this->getMockForTrait(
            '\Common\Controller\Traits\GenericEditAction',
            [],
            '',
            true,
            true,
            true,
            ['renderSection']
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

        $this->sut->editAction();
    }
}
