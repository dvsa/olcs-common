<?php

/**
 * Form Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\FormHelperService;
use Mockery as m;

/**
 * Form Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FormHelperServiceTest extends PHPUnit_Framework_TestCase
{
    public function testAlterElementLabelWithNoType()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Replaced label');

        $helper->alterElementLabel($element, 'Replaced label');
    }

    public function testAlterElementLabelWithAppend()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('My labelAppended label');

        $helper->alterElementLabel($element, 'Appended label', 1);
    }

    public function testAlterElementLabelWithPrepend()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Prepended labelMy label');

        $helper->alterElementLabel($element, 'Prepended label', 2);
    }
}
