<?php

namespace CommonTest\Service\Document;

use Common\Service\Document\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = new Document();
    }

    public function testGetBookmarkQueriesForNoBookmarks()
    {
        $content = '';

        $queryData = $this->service->getBookmarkQueries(
            'application/rtf',
            $content,
            []
        );
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForStaticBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart letter_date_add_14_days} {\*\bkmkend letter_date_add_14_days}.
Boomkark 2: {\*\bkmkstart todays_date}{\*\bkmkend todays_date}
TXT;

        $queryData = $this->service->getBookmarkQueries(
            'application/rtf',
            $content,
            []
        );
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForDynamicConcreteBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart caseworker_name} {\*\bkmkend caseworker_name}
Bookmark 2: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}
TXT;

        $queryData = $this->service->getBookmarkQueries(
            'application/rtf',
            $content,
            [
                'user' => 1,
                'licence' => 123
            ]
        );

        $this->assertArrayHasKey('caseworker_name', $queryData);
        $this->assertArrayHasKey('licence_number', $queryData);
    }

    public function testGetBookmarkQueriesForDynamicTextBlockBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart para_one} {\*\bkmkend para_one}
Bookmark 2: {\*\bkmkstart para_two} {\*\bkmkend para_two}
Bookmark 3: {\*\bkmkstart para_three} {\*\bkmkend para_three}
TXT;

        $queryData = $this->service->getBookmarkQueries(
            'application/rtf',
            $content,
            [
                'bookmarks' => [
                    'para_one' => [1],
                    'para_three' => [2]
                ]
            ]
        );

        $this->assertArrayHasKey('para_one', $queryData);
        $this->assertArrayHasKey('para_three', $queryData);

        // we didn't supply any bookmark data for para two so we'd
        // expect it to not be in the query
        $this->assertArrayNotHasKey('para_two', $queryData);
    }

    public function testPopulateBookmarksWithStaticBookmarks()
    {
        $content = "Bookmark 1: {\*\bkmkstart todays_date} {\*\bkmkend todays_date}.";

        $replaced = $this->service->populateBookmarks(
            'application/rtf',
            $content,
            []
        );

        // @NOTE: ideally we'd mock a todays_date bookmark instead of
        // using a real (and especially a date sensitive) one...
        $date = date("d/m/Y");

        $this->assertEquals(
            "Bookmark 1: " . $date . ".",
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarks()
    {
        $content = "Bookmark 1: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}.";

        $replaced = $this->service->populateBookmarks(
            'application/rtf',
            $content,
            [
                'licence_number' => [
                    'licNo' => 1234
                ]
            ]
        );

        $this->assertEquals(
            "Bookmark 1: 1234.",
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksButNoData()
    {
        $content = "Bookmark 1: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}.";

        $replaced = $this->service->populateBookmarks(
            'application/rtf',
            $content,
            []
        );

        $this->assertEquals(
            $content,
            $replaced
        );
    }
}
