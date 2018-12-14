<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\SystemInfoMessageLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * @covers Common\Service\Table\Formatter\SystemInfoMessageLink
 */
class SystemInfoMessageLinkTest extends TestCase
{
    const EXPECT_URL = 'unit_Url';
    const ID = 9999;

    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        $data['id'] = self::ID;

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'admin-dashboard/admin-system-info-message',
                        [
                            'action' => 'edit',
                            'msgId' => self::ID,
                        ]
                    )
                    ->andReturn(self::EXPECT_URL)
                    ->getMock()
            )
            ->getMock();

        static::assertEquals(
            $expect,
            SystemInfoMessageLink::format($data, [], $sm)
        );
    }

    public function dpTestFormat()
    {
        return [
            [
                'data' => [
                    'description' => 'unit_Desc',
                    'isActive' => true,
                ],
                'expect' => '<a href="' . self::EXPECT_URL . '" class="js-modal-ajax">unit_Desc</a>' .
                    ' <span class="status green">ACTIVE</span>',
            ],
            [
                'data' => [
                    'description' => str_repeat('X', SystemInfoMessageLink::MAX_DESC_LEN + 1),
                    'isActive' => false,
                ],
                'expect' =>
                    '<a href="' . self::EXPECT_URL . '" class="js-modal-ajax">' .
                        str_repeat('X', SystemInfoMessageLink::MAX_DESC_LEN) . '...' .
                    '</a>' .
                    ' <span class="status grey">INACTIVE</span>',
            ],
        ];
    }
}
