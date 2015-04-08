<?php

namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TransportManagerNameTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class TransportManagerNameInternalTest extends TestCase
{
    public function testFormat()
    {
        $mockUrlHelper = $this->getMock('\stdClass', array('fromRoute'));
        $mockUrlHelper->expects($this->once())
            ->method('fromRoute')
            ->with('transport-manager', ['transportManager' => 766], [], true)
            ->willReturn('a-url');

        $mockServerManager = $this->getMock('\stdClass', array('get'));
        $mockServerManager->expects($this->once())
            ->method('get')
            ->willReturn($mockUrlHelper);

        $sut = new \Common\Service\Table\Formatter\TransportManagerNameInternal();
        $data = [
            'forename' => 'Arthur',
            'familyName' => 'Smith',
            'status' => [
                'id' => \Common\Service\Entity\TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'a description',
            ],
            'transportManager' => [
                'id' => 766,
            ]
        ];

        $formatted = $sut->format($data, null, $mockServerManager);

        // assert that the hyperlink is in the string, not asserting foramtting of name, etc as that is covered
        // in TransportManagerNameTest
        $this->assertNotFalse(strpos($formatted, '<a href="a-url">'));
    }
}
