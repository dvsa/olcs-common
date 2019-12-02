<?php

namespace CommonTest\Service\Qa\Custom\Common;

use Common\Service\Qa\Custom\Common\DateBeforeValidator;
use Common\Service\Qa\DateTimeFactory;
use DateTime;
use IntlDateFormatter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\View\Helper\DateFormat;

/**
 * DateBeforeValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateBeforeValidatorTest extends MockeryTestCase
{
    const DATE_MUST_BE_BEFORE_DATE_STRING = '2020-01-03';

    private $dateFormat;

    private $dateTimeFactory;

    private $dateBeforeValidator;

    public function setUp()
    {
        $options = [
            'dateMustBeBefore' => self::DATE_MUST_BE_BEFORE_DATE_STRING
        ];

        $this->dateFormat = m::mock(DateFormat::class);

        $this->dateTimeFactory = m::mock(DateTimeFactory::class);

        $this->dateBeforeValidator = new DateBeforeValidator(
            $this->dateFormat,
            $this->dateTimeFactory,
            $options
        );
    }

    /**
     * @dataProvider dpIsValidTrue
     */
    public function testIsValidTrue($date)
    {
        $this->assertTrue(
            $this->dateBeforeValidator->isValid($date)
        );
    }

    public function dpIsValidTrue()
    {
        return [
            ['2020-01-02'],
            ['2020-01-01'],
            ['2019-12-31'],
            ['2019-12-30'],
        ];
    }

    /**
     * @dataProvider dpIsValidFalse
     */
    public function testIsValidFalse($date)
    {
        $dateMustBeBeforeDateTime = m::mock(DateTime::class);

        $formattedDateMustBeBefore = '3 Jan 2020';

        $this->dateTimeFactory->shouldReceive('create')
            ->with(self::DATE_MUST_BE_BEFORE_DATE_STRING)
            ->once()
            ->andReturn($dateMustBeBeforeDateTime);

        $this->dateFormat->shouldReceive('__invoke')
            ->with($dateMustBeBeforeDateTime, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE)
            ->once()
            ->andReturn($formattedDateMustBeBefore);

        $this->assertFalse(
            $this->dateBeforeValidator->isValid($date)
        );

        $expectedMessages = [
            DateBeforeValidator::ERR_DATE_NOT_BEFORE => 'Date is too far away'
        ];

        $this->assertEquals(
            $expectedMessages,
            $this->dateBeforeValidator->getMessages()
        );

        $this->assertEquals(
            $formattedDateMustBeBefore,
            $this->dateBeforeValidator->__get('dateMustBeBefore')
        );
    }

    public function dpIsValidFalse()
    {
        return [
            ['2020-01-03'],
            ['2020-01-04'],
            ['2020-01-05'],
            ['2020-02-01'],
            ['2021-03-28'],
            ['2022-01-01'],
        ];
    }
}
