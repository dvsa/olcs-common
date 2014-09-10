<?php

/**
 * New Vrm Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\NewVrm;

/**
 * New Vrm Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NewVrmTest extends PHPUnit_Framework_TestCase
{
    private $validator;

    public function setUp()
    {
        $this->validator = new NewVrm();
    }

    /**
     * @group NewVrmValidator
     * @dataProvider provider
     */
    public function testValidator($type, $vrms, $value, $expected)
    {
        $this->validator->setType($type);
        $this->validator->setVrms($vrms);
        $outcome = $this->validator->isValid($value);

        $this->assertEquals($expected, $outcome);
    }

    public function provider()
    {
        return array(
            array(
                'Licence',
                array(),
                'ABC123',
                true
            ),
            array(
                'Application',
                array(),
                'ABC123',
                true
            ),
            array(
                'Licence',
                array('Foo'),
                'ABC123',
                true
            ),
            array(
                'Application',
                array('Foo'),
                'ABC123',
                true
            ),
            array(
                'Licence',
                array('Foo', 'Bar'),
                'Foo',
                false
            ),
            array(
                'Application',
                array('Foo', 'Bar'),
                'Foo',
                false
            )
        );
    }
}
