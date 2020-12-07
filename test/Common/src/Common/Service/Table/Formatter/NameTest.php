<?php

/**
 * Name formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Common\Service\Table\Formatter\Name;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Name formatter test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class NameTest extends MockeryTestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group AddressFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, Name::format($data, [], null));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                array(
                    'forename' => 'A',
                    'familyName' => 'Person',
                    'title' => array(
                        'description' => 'Mr'
                    )
                ),
                'Mr A Person'
            ),
            array(
                array(
                    'forename' => 'A',
                    'familyName' => 'Person',
                ),
                'A Person'
            )
        );
    }

    public function testFormatNestedData()
    {
        $data = [
            'foo' => [
                'forename' => 'John',
                'familyName' => 'Smith',
            ]
        ];
        $this->assertEquals('John Smith', Name::format($data, ['name' => 'foo']));
    }

    public function testFormatDeepNestedData()
    {
        $data = [
            'foo' => [
                'name' => [
                    'forename' => 'John',
                    'familyName' => 'Smith',
                ]
            ]
        ];

        $mockHelper = $this->createPartialMock(DataHelperService::class, array('fetchNestedData'));

        $mockHelper->expects($this->once())
            ->method('fetchNestedData')
            ->with($data, 'foo->name')
            ->willReturn($data['foo']['name']);

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->with('Helper\Data')
            ->will($this->returnValue($mockHelper));

        $this->assertEquals('John Smith', Name::format($data, ['name' => 'foo->name'], $sm));
    }
}
