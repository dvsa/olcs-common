<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TextBlock;

/**
 * Text block test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TextBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderConcatenatesParagraphsWithNewlines()
    {
        $bookmark = new TextBlock();
        $bookmark->setData(
            [
                ['paraText' => 'Para 1'],
                ['paraText' => 'Para 2'],
                ['paraText' => 'Para 3']
            ]
        );

        $result = $bookmark->render();

        $this->assertEquals(
            "Para 1\nPara 2\nPara 3",
            $result
        );
    }

    public function testRenderWithStringDataJustReturnsString()
    {
        $bookmark = new TextBlock();
        $bookmark->setData('foo bar');

        $result = $bookmark->render();

        $this->assertEquals('foo bar', $result);
    }
}
