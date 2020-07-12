<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\YesNo;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\YesNo
 */
class YesNoTest extends MockeryTestCase
{
    /** @var  m\MockInterface */
    private $mockSm;
    /** @var  m\MockInterface */
    private $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->mockSm->shouldReceive('get')->once()->with('translator')->andReturn($this->mockTranslator);
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group YesNoFormatter
     *
     * @dataProvider dpTestFormatByName
     */
    public function testFormatByName($data, $column, $expected)
    {
        $this->mockTranslator
            ->shouldReceive('translate')->once()->with('common.table.' . $expected)->andReturn('EXPECT');

        static::assertEquals('EXPECT', YesNo::format($data, $column, $this->mockSm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function dpTestFormatByName()
    {
        return [
            [
                'data' => ['yesorno' => 1],
                'column' => ['name' => 'yesorno'],
                'expect' => 'Yes',
            ],
            [
                'data' => ['yesorno' => 0],
                'column' => ['name' => 'yesorno'],
                'expect' => 'No',
            ],
            [
                'data' => ['yesorno' => 'Y'],
                'column' => ['name' => 'yesorno'],
                'expect' => 'Yes',
            ],
            [
                'data' => ['yesorno' => 'N'],
                'column' => ['name' => 'yesorno'],
                'expect' => 'No',
            ],
            [
                'data' => ['yesorno' => 'something'],
                'column' => ['name' => 'yesorno'],
                'expect' => 'Yes',
            ],
        ];
    }

    public function testFormatBySlack()
    {
        $data = ['data'];
        $column = [
            'stack' => 'fieldset->fieldset2->field',
        ];

        $this->mockTranslator->shouldReceive('translate')->once()->with('common.table.Yes')->andReturn('EXPECT');
        $this->mockSm
            ->shouldReceive('get')
            ->with('Helper\Stack')
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('getStackValue')
                    ->once()
                    ->with($data, ['fieldset', 'fieldset2', 'field'])
                    ->andReturn('Y')
                    ->getMock()
            );

        static::assertEquals('EXPECT', YesNo::format($data, $column, $this->mockSm));
    }
}
