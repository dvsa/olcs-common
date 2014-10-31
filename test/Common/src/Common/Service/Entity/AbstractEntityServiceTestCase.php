<?php

/**
 * Abstract Entity Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Entity;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;

/**
 * Abstract Entity Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractEntityServiceTestCase extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    protected $restHelper;

    protected function setUp()
    {
        $this->restHelper = $this->getMock('\stdClass', array('makeRestCall'));

        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);
        $this->sm->setService('Helper\Rest', $this->restHelper);

        $this->sut->setServiceLocator($this->sm);
    }

    protected function expectOneRestCall($entity, $method, $data, $bundle = null)
    {
        $expectation = $this->restHelper->expects($this->once())->method('makeRestCall');

        if ($bundle !== null) {
            return $expectation->with($entity, $method, $data, $bundle);
        }

        return $expectation->with($entity, $method, $data);
    }

    protected function mockDate($date)
    {
        $mockDateHelper = $this->getMock('\stdClass', ['getDate']);
        $mockDateHelper->expects($this->any())
            ->method('getDate')
            ->will($this->returnValue($date));

        $this->sm->setService('Helper\Date', $mockDateHelper);
    }
}
