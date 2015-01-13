<?php

/**
 * Vrm Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Form\Elements\Validators\Vrm;

/**
 * Vrm Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VrmTest extends MockeryTestCase
{
    private $validator;

    public function setUp()
    {
        $this->validator = new Vrm();
    }

    /**
     * @dataProvider provider
     */
    public function testValidator($input, $isValid, $message = null)
    {
        $outcome = $this->validator->isValid($input);

        $this->assertEquals($isValid, $outcome);
    }

    public function provider()
    {
        return [
            ['U3', true],
            ['Z1', false],
            ['BD100', true],
            ['BQ000', false],
            ['BD1456', true],
            ['QD1456', false],
            ['K5012', true],
            ['Z5012', false],
            ['KL55', true],
            ['KL05', false],
            ['F5DEF', true],
            ['Z5DEF', false],
            ['G50DEF', true],
            ['Q05DEF', false],
            ['AB01QQ', true],
            ['AB01Q1', false],
            ['01AB23', true],
            ['012A34', false],
            ['KM51ABC', true],
            ['KMM1ABC', false],
            ['012345Z', true],
            ['012345X', false],
            ['Q5ABC', true],
            ['Z5ABC', false],
            ['Q10ABC', true],
            ['Q00ABC', false],
            // exceptions
            ['11', true],
            ['1CZS', true]
        ];
    }
}
