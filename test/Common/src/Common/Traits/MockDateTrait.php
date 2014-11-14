<?php

/**
 * Mock Date Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Traits;

/**
 * Mock Date Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait MockDateTrait
{

    /**
     * Helper method
     */
    protected function mockDate($date)
    {
        $mockDateHelper = $this->getMock('\stdClass', ['getDate']);
        $mockDateHelper->expects($this->any())
            ->method('getDate')
            ->will($this->returnValue($date));

        $this->sm->setService('Helper\Date', $mockDateHelper);
    }
}
