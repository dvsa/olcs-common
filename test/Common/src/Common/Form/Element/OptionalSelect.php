<?php

namespace CommonTest\Common\Form\Element;

use PHPUnit\Framework\TestCase;

class OptionalSelect extends TestCase
{
    public function testSelectNotRequired()
    {
        $select = new \Common\Form\Element\OptionalSelect();

        $this->assertFalse($select->getInputSpecification()['required']);
    }
}
