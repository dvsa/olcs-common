<?php

namespace CommonTest\Service\Document\Parser;

use Common\Service\Document\Parser\RtfParser;

class RtfParserTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractTokens()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart bookmark_one}{\*\bkmkend bookmark_one}
Bookmark 2: {\*\bkmkstart bookmark_two} {\*\bkmkend bookmark_two}
Bookmark 3: {\*\bkmkstart bookmark_three}
{\*\bkmkend bookmark_three}
TXT;

        $parser = new RtfParser();

        $tokens = [
            'bookmark_one',
            'bookmark_two',
            'bookmark_three'
        ];

        $this->assertEquals($tokens, $parser->extractTokens($content));
    }
}
