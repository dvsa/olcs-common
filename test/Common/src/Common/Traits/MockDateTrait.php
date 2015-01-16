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
        $dateObj = new \DateTime($date);

        $mockDateHelper = $this->getMock('\stdClass', ['getDate', 'getDateObject']);
        $mockDateHelper->expects($this->any())
            ->method('getDate')
            ->will($this->returnValue($date));
        $mockDateHelper->expects($this->any())
            ->method('getDateObject')
            ->will($this->returnValue($dateObj));

        $this->sm->setService('Helper\Date', $mockDateHelper);
    }
}
