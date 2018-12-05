<?php

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Types\PostcodeSearch;
use Zend\Form\Element\Text;

/**
 * PostcodeTest
 */
class PostcodeTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorPostcodeElement()
    {
        $sut = new PostcodeSearch('foo');

        $postcodeElement = $sut->get('postcode');
        $this->assertInstanceOf(Text::class, $postcodeElement);

        $attributes = $postcodeElement->getAttributes();
        $this->assertArraySubset(
            [
                'class' => 'short js-input',
                'data-container-class' => 'inline',
            ],
            $attributes
        );
        $this->assertRegExp('/postcodeInput[0-9]/', $attributes['id']);
    }

    public function testConstructorPostcodeElementNumberIsIncremented()
    {
        $sut1 = new PostcodeSearch('foo');
        $sut2 = new PostcodeSearch('bar');

        $this->assertNotEquals(
            $sut1->get('postcode')->getAttributes()['id'],
            $sut2->get('postcode')->getAttributes()['id']
        );
    }
}
