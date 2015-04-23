<?php

/**
 * Test TransportManagerApplicationStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use \Common\View\Helper\TransportManagerApplicationStatus;
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Test TransportManagerApplicationStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplicationStatusTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->sut = new TransportManagerApplicationStatus();
    }

    public function dataProviderRender()
    {
        return [
            [' orange', TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE],
            [' red', TransportManagerApplicationEntityService::STATUS_INCOMPLETE],
            [' green', TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED],
            [' green', TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION],
            [' orange', TransportManagerApplicationEntityService::STATUS_TM_SIGNED],
            [' green', TransportManagerApplicationEntityService::STATUS_RECEIVED],
            'invalidStatus' => ['', 'foo'],
        ];
    }

    /**
     * @dataProvider dataProviderRender
     * @param type $expectedClass
     * @param type $status
     */
    public function testRender($expectedClass, $status)
    {
        $html = $this->sut->render($status, $status);

        $this->assertEquals('<span class="status'. $expectedClass .'">'. $status .'</span>', $html);
    }

    /**
     * @dataProvider dataProviderRender
     * @param type $expectedClass
     * @param type $status
     */
    public function testInvoke($expectedClass, $status)
    {
        $html = $this->sut->__invoke($status, $status);

        $this->assertEquals('<span class="status'. $expectedClass .'">'. $status .'</span>', $html);
    }
}
