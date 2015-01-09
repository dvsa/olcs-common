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
            ['11', true]
        ];
    }
}
