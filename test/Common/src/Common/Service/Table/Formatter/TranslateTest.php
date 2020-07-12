<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Common\Service\Table\Formatter\Translate;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Common\Service\Table\Formatter\Translate
 */
class TranslateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->createPartialMock(Translator::class, ['translate']);

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return strtoupper($string);
                    }
                )
            );

        $hldData = new DataHelperService();

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    ['translator', $mockTranslator],
                    ['Helper\Data', $hldData],
                ]
            );

        $this->assertEquals($expected, Translate::format($data, $column, $sm));
    }

    /**TaskIdentifierTest
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            [
                'data' => ['test' => 'foo'],
                'column' => ['name' => 'test'],
                'expect' => 'FOO',
            ],
            [
                'data' => ['test' => 'foo'],
                'column' => ['content' => 'test'],
                'expect' => 'TEST',
            ],
            [
                'data' => ['test' => 'foo'],
                'column' => [],
                'expect' => '',
            ],
            [
                'data' => [
                    'test' => ['foo' => 'bar']
                ],
                'column' => ['name' => 'test->foo'],
                'expect' => 'BAR',
            ],
        ];
    }
}
