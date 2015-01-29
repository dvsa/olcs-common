<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\SectionConfig;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Section Config Test
 * @package CommonTest\Service\Data
 */
class SectionConfigTest extends MockeryTestCase
{
    public function testGetAll()
    {
        $sut = new SectionConfig();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')->with('Processing\VariationSection')
            ->getMock();
        $sut->setServiceLocator($sm);

        $all = $sut->getAll();

        $totalSections = count($all);

        // undertakings sections should have all sections bar itself as a prerequisite
        $this->assertEquals(
            ($totalSections - 1),
            count($all['undertakings']['prerequisite'])
        );
    }
}
