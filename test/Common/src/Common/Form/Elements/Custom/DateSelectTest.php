<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\DateSelect;

/**
 * @covers \Common\Form\Elements\Custom\DateSelect
 */
class DateSelectTest extends \PHPUnit\Framework\TestCase
{
    /** @var  DateSelect */
    private $sut;

    public function setUp()
    {
        $this->sut = new DateSelect('foo');
    }

    public function testGetInputSpecification()
    {
        $this->sut->setOptions([]);

        $spec = $this->sut->getInputSpecification();

        $this->assertEquals('foo', $spec['name']);
        $this->assertEquals(null, $spec['required']);
        $this->assertCount(1, $spec['validators']);
        $this->assertCount(1, $spec['filters']);
        $this->assertInstanceOf('Zend\Validator\Date', $spec['validators'][0]);

        // Test the filter
        $this->assertNull($spec['filters'][0]['options']['callback']('foo'));
        $this->assertNull($spec['filters'][0]['options']['callback'](['year' => '2015']));
        $this->assertNull($spec['filters'][0]['options']['callback'](['year' => '2015', 'month' => '02']));
        $this->assertEquals(
            '2015-02-01',
            $spec['filters'][0]['options']['callback'](['year' => '2015', 'month' => '02', 'day' => '01'])
        );
        static::assertEquals('date-hint', $this->sut->getOption('hint'));
    }

    public function testSetOptionsMinAndMaxYear()
    {
        $options = [
            'max_year_delta' => '+5',
            'min_year_delta' => '-5'
        ];

        $year = date('Y');

        $this->sut->setOptions($options);

        $this->assertEquals(($year + 5), $this->sut->getMaxYear());
        $this->assertEquals(($year - 5), $this->sut->getMinYear());
    }

    public function testSetOptionsMaxYear()
    {
        $options = [
            'max_year_delta' => '+5'
        ];

        $year = date('Y');

        $this->sut->setOptions($options);

        $this->assertEquals(($year + 5), $this->sut->getMaxYear());
        $this->assertEquals($year, $this->sut->getMinYear());
    }

    public function testSetOptionsMinYear()
    {
        $options = [
            'min_year_delta' => '-5'
        ];

        $year = date('Y');

        $this->sut->setOptions($options);

        $this->assertEquals($year, $this->sut->getMaxYear());
        $this->assertEquals(($year - 5), $this->sut->getMinYear());
    }

    public function testSetOptionsDefaultDateNow()
    {
        $options = [
            'hint' => 'unit_Hint',
            'default_date' => 'now',
        ];

        $this->sut->setOptions($options);

        $this->assertEquals(date('Y-m-d'), $this->sut->getValue());
        static::assertEquals('unit_Hint', $this->sut->getOption('hint'));
    }

    public function testSetOptionsDefaultDate()
    {
        $options = [
            'default_date' => '+3 months'
        ];

        $this->sut->setOptions($options);

        $this->assertEquals(date('Y-m-d', strtotime('+3 months')), $this->sut->getValue());
    }
}
