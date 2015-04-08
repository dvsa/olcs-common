<?php

namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TransportManagerNameTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class TransportManagerNameTest extends TestCase
{
    public function testFormat()
    {
        $mockUrlHelper = $this->getMock('\stdClass', array('fromRoute'));
        $mockUrlHelper->expects($this->once())
            ->method('fromRoute')
            ->with(null, ['action' => 'postal-application'], [], true)
            ->willReturn('a-url');

        $mockServerManager = $this->getMock('\stdClass', array('get'));
        $mockServerManager->expects($this->once())
            ->method('get')
            ->willReturn($mockUrlHelper);

        $sut = new \Common\Service\Table\Formatter\TransportManagerName();
        $data = [
            'forename' => 'Arthur',
            'familyName' => 'Smith',
            'status' => [
                'id' => \Common\Service\Entity\TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'a description',
            ]
        ];

        $formatted = $sut->format($data, null, $mockServerManager);

        $this->assertEquals(
            '<b><a href="a-url">Arthur Smith</a></b> <span class="status green">a description</span>',
            $formatted
        );
    }
}
