<?php

/**
 * AddressLines formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Common\Service\Table\Formatter\AddressLines;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AddressLines formatter test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class AddressLinesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group AddressLinesFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertEquals($expected, AddressLines::format($data, $column));
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
                array('addressLine1' => 'foo'), array(), '<p>foo</p>'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar'), array(), '<p>foo</p>'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'town' => 'cake'),
                array(),
                '<p>foo,<br />cake</p>'
            ),
            array(
                array(
                    'addressLine1' => 'foo',
                    'addressLine2' => 'bar',
                    'addressLine3' => 'cake',
                    'town' => 'fourth'
                ),
                array(),
                '<p>foo,<br />fourth</p>'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'addressLine3' => 'cake'),
                array('addressFields' => array('addressLine1', 'addressLine2')),
                '<p>foo,<br />bar</p>'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'addressLine3' => 'cake'),
                array('addressFields' => 'FULL'),
                '<p>foo,<br />bar,<br />cake</p>'
            ),
            array(
                array(
                    'address' => array(
                        'addressLine1' => 'foo',
                        'addressLine2' => 'bar',
                        'addressLine3' => 'cake',
                        'town' => 'fourth'
                    )
                ),
                array(
                    'name' => 'address'
                ),
                '<p>foo,<br />fourth</p>'
            )
        );
    }

    /**
     * Test the format method with nested keys
     *
     * @group Formatters
     * @group AddressLinesFormatter
     */
    public function testFormatWithNestedKeys()
    {
        $mockHelper = $this->createPartialMock(DataHelperService::class, array('fetchNestedData'));

        $mockHelper->expects($this->once())
            ->method('fetchNestedData')
            ->with(['foo' => 'bar'], 'bar->baz')
            ->willReturn(['addressLine1' => 'address 1']);

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->with('Helper\Data')
            ->will($this->returnValue($mockHelper));

        $data = [
            'foo' => 'bar'
        ];
        $columns = [
            'name' => 'bar->baz'
        ];
        $this->assertEquals('<p>address 1</p>', AddressLines::format($data, $columns, $sm));
    }
}
