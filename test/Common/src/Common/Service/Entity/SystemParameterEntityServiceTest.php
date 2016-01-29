<?php

/**
 * System Parameter Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\SystemParameterEntityService;

/**
 * System Parameter Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SystemParameterEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new SystemParameterEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetValueWithNoMatch()
    {
        $this->expectOneRestCall('SystemParameter', 'GET', 123)
            ->willReturn(false);

        $this->assertEquals(false, $this->sut->getValue(123));
    }

    /**
     * @group entity_services
     */
    public function testGetValueWithMatch()
    {
        $this->expectOneRestCall('SystemParameter', 'GET', 123)
            ->willReturn(['paramValue' => 456]);

        $this->assertEquals(456, $this->sut->getValue(123));
    }
}
