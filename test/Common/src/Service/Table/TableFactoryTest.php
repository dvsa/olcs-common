<?php

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table;

use Common\Service\Table\TableFactory;

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));

        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue(array()));

        $tableFactory = new TableFactory();

        $table = $tableFactory->createService($serviceLocator);

        $this->assertTrue($table instanceof \Common\Service\Table\TableBuilder);
    }

}
