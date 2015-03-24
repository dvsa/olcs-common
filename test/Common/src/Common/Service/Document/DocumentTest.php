<?php

namespace CommonTest\Service\Document;

use Common\Service\Document\Document;
use Dvsa\Jackrabbit\Data\Object\File;

/**
 * Document service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = new Document();
    }

    public function testGetBookmarkQueriesForNoBookmarks()
    {
        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent('');

        $queryData = $this->service->getBookmarkQueries($file, []);
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForStaticBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart letter_date_add_14_days} {\*\bkmkend letter_date_add_14_days}.
Boomkark 2: {\*\bkmkstart todays_date}{\*\bkmkend todays_date}
TXT;
        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $queryData = $this->service->getBookmarkQueries($file, []);
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForDynamicConcreteBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart caseworker_name} {\*\bkmkend caseworker_name}
Bookmark 2: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}
TXT;
        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $queryData = $this->service->getBookmarkQueries(
            $file,
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
        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $queryData = $this->service->getBookmarkQueries(
            $file,
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

        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $replaced = $this->service->populateBookmarks(
            $file,
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

        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $replaced = $this->service->populateBookmarks(
            $file,
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

        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $replaced = $this->service->populateBookmarks(
            $file,
            []
        );

        $this->assertEquals(
            $content,
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksImplementingDateAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart Serial_Num} {\*\bkmkend Serial_Num}.";

        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $helperMock = $this->getMock('Common\Service\Helper\DateHelperService');

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('Helper\Date')
            ->willReturn($helperMock);

        $this->service->setServiceLocator($serviceLocator);

        $this->service->populateBookmarks(
            $file,
            []
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksImplementingFileStoreAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart TC_SIGNATURE} {\*\bkmkend TC_SIGNATURE}.";

        $file = new File();
        $file->setMimeType('application/rtf');
        $file->setContent($content);

        $helperMock = $this->getMock('\stdClass');

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('ContentStore')
            ->willReturn($helperMock);

        $this->service->setServiceLocator($serviceLocator);

        $this->service->populateBookmarks(
            $file,
            []
        );
    }
}
