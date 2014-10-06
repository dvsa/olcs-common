<?php

/**
 * Additional PSV discs tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\AdditionalPsvDiscs;

/**
 * Additional PSV discs tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AdditionalPsvDiscsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Subject under test
     *
     * @var \Common\Form\Elements\InputFilters\AdditionalPsvDiscs
     */
    private $sut;

    /**
     * test setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->sut = new AdditionalPsvDiscs();
    }

    /**
     * @group input_filters
     * @group additional_psv_discs_input_filters
     */
    public function testGetInputSpecification()
    {
        $spec = $this->sut->getInputSpecification();

        $this->assertFalse($spec['required']);
        $this->assertTrue($spec['continue_if_empty']);
        $this->assertFalse($spec['allow_empty']);

        $this->assertCount(3, $spec['validators']);

        $this->assertInstanceOf('\Zend\Validator\Digits', $spec['validators'][0]);
        $this->assertInstanceOf('\Zend\Validator\GreaterThan', $spec['validators'][1]);
        $this->assertInstanceOf(
            '\Common\Form\Elements\Validators\AdditionalPsvDiscsValidator',
            $spec['validators'][2]
        );
    }
}
