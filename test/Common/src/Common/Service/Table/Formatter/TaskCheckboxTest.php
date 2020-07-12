<?php

/**
 * Task checkbox formatter tests
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\ContentHelper;
use Common\Service\Table\Formatter\TaskCheckbox;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Task checkbox formatter tests
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskCheckboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider notClosedProvider
     */
    public function testFormatNotClosed($data)
    {
        $column = [];

        $mockTableService = $this->createPartialMock(ContentHelper::class, array('replaceContent'));

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->with('TableBuilder')
            ->will($this->returnValue($mockTableService));

        $mockTableService->expects($this->once())
            ->method('replaceContent')
            ->with('{{[elements/checkbox]}}', $data)
            ->will($this->returnValue('checkbox markup'));

        $this->assertEquals('checkbox markup', TaskCheckbox::format($data, $column, $sm));
    }

    public function notClosedProvider()
    {
        return [
            'N' => [
                [
                    'id' => 69,
                    'isClosed' => 'N',
                ],
            ],
            'not set' => [
                [
                    'id' => 69,
                ],
            ],
            'null' => [
                [
                    'id' => 69,
                    'isClosed' => null,
                ],
            ],
        ];
    }

    public function testFormatClosed()
    {
        $data = [
            'id' => 69,
            'isClosed' => 'Y',
        ];

        $column = [];

        $this->assertEquals('', TaskCheckbox::format($data, $column));
    }
}
