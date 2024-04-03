<?php

/**
 * Test AddTags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use Common\View\Helper\AddTags;

/**
 * Test AddTags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddTagsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideStrings
     */
    public function testInvoke($input, $expected): void
    {
        $helper = new AddTags();
        $output = $helper->__invoke($input);
        $this->assertEquals($expected, $output);
    }

    public function provideStrings()
    {
        return [
            ['no text to replace', 'no text to replace'],
            ['text to replace (if applicable)', 'text to replace <span class=js-hidden>(if applicable)</span>'],
            [
                'multiline to replace (if
                applicable)',
                'multiline to replace <span class=js-hidden>(if applicable)</span>'
            ]
        ];
    }
}
