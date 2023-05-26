<?php

/**
 * Address formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Common\Service\Table\Formatter\Address;
use Mockery as m;

/**
 * Address formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressTest extends \PHPUnit\Framework\TestCase
{
    protected $dataHelper;

    protected function setUp(): void
    {
        $this->dataHelper = m::mock(DataHelperService::class);
        $this->sut = new Address($this->dataHelper);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group AddressFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertEquals($expected, $this->sut->format($data, $column));
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
                array('addressLine1' => 'foo'), array(), 'foo'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar'), array(), 'foo'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'town' => 'cake'), array(), 'foo, cake'
            ),
            array(
                array(
                    'addressLine1' => 'foo',
                    'addressLine2' => 'bar',
                    'addressLine3' => 'cake',
                    'town' => 'fourth'
                ),
                array(),
                'foo, fourth'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'addressLine3' => 'cake'),
                array('addressFields' => array('addressLine1', 'addressLine2')),
                'foo, bar'
            ),
            array(
                array('addressLine1' => 'foo', 'addressLine2' => 'bar', 'addressLine3' => 'cake'),
                array('addressFields' => 'FULL'),
                'foo, bar, cake'
            ),
            "BRIEF with postCode" => array(
                array(
                    'addressLine1' => 'foo',
                    'addressLine2' => 'bar',
                    'addressLine3' => 'cake',
                    'addressLine4' => 'baz',
                    'town' => 'spam',
                    'postcode' => 'eggs',
                    'countryCode' => 'ham',
                ),
                array('addressFields' => 'BRIEF'),
                'foo, spam, eggs'
            ),
            "BRIEF with blank postCode" => array(
                array(
                    'addressLine1' => 'foo',
                    'addressLine2' => 'bar',
                    'addressLine3' => 'cake',
                    'addressLine4' => 'baz',
                    'town' => 'spam',
                    'postcode' => '',
                    'countryCode' => 'ham',
                ),
                array('addressFields' => 'BRIEF'),
                'foo, spam'
            ),
            "BRIEF without postCode" => array(
                array(
                    'addressLine1' => 'foo',
                    'addressLine2' => 'bar',
                    'addressLine3' => 'cake',
                    'addressLine4' => 'baz',
                    'town' => 'spam',
                    'countryCode' => 'ham',
                ),
                array('addressFields' => 'BRIEF'),
                'foo, spam'
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
                'foo, fourth'
            )
        );
    }

    /**
     * Test the format method with nested keys
     *
     * @group Formatters
     * @group AddressFormatter
     */
    public function testFormatWithNestedKeys()
    {
        $this->dataHelper->shouldReceive('fetchNestedData')
            ->with(['foo' => 'bar'], 'bar->baz')
            ->andReturn(['addressLine1' => 'address 1'])
            ->once();

        $data = [
            'foo' => 'bar'
        ];
        $columns = [
            'name' => 'bar->baz'
        ];
        $this->assertEquals('address 1', $this->sut->format($data, $columns));
    }
}
