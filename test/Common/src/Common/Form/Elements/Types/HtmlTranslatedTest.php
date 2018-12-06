<?php

/**
 * HtmlTranslatedTest
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Types\HtmlTranslated;

/**
 * HtmlTranslatedTest
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HtmlTranslatedTest extends PHPUnit_Framework_TestCase
{
    /**
     * Placeholder
     * 
     */
    public function testElement()
    {
        $element = new HtmlTranslated();
        $this->assertInstanceOf('Common\Form\Elements\Types\HtmlTranslated', $element);
    }
}
