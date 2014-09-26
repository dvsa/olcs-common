<?php

namespace CommonTest\Service\Document\Parser;

use Common\Service\Document\Parser\RtfParser;

/**
 * RTF parser test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
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

    public function testReplace()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart bookmark_one}{\*\bkmkend bookmark_one}
Bookmark 2: {\*\bkmkstart bookmark_two} {\*\bkmkend bookmark_two}
Bookmark 3: {\*\bkmkstart bookmark_three}
{\*\bkmkend bookmark_three}
Bookmark 3 Repeat: {\*\bkmkstart bookmark_three}
{\*\bkmkend bookmark_three}
Date: {\*\bkmkstart letter_date_add_14_days}
{\*\bkmkend letter_date_add_14_days}
TXT;

        $expected = <<<TXT
Bookmark 1: Some Content\par With newlines
Bookmark 2: {\*\bkmkstart bookmark_two} {\*\bkmkend bookmark_two}
Bookmark 3: Three
Bookmark 3 Repeat: Three
Date: Today
TXT;

        $parser = new RtfParser();

        $data = [
            "bookmark_one" => "Some Content\nWith newlines",
            "bookmark_three" => "Three",
            "letter_date_add_14_days" => "Today"
        ];

        $this->assertEquals(
            $expected,
            $parser->replace($content, $data)
        );
    }
}
