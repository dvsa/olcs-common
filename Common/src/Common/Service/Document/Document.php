<?php

namespace Common\Service\Document;

use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

/**
 * Document generation service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Document
{
    public function getBookmarkQueries(ContentStoreFile $file, $data)
    {
        $queryData = [];

        $tokens = $this->getParser($file->getMimeType())
            ->extractTokens($file->getContent());

        $bookmarks = $this->getBookmarks($tokens);

        foreach ($bookmarks as $token => $bookmark) {

            // we don't need to query if the bookmark is static (i.e.
            // doesn't rely on any backend information)
            if ($bookmark->isStatic()) {
                continue;
            }

            $query = $bookmark->getQuery($data);

            // we need to allow for the fact the bookmark might not want
            // to actually generate a query in which case it can return
            // a null value
            if ($query !== null) {
                $queryData[$token] = $query;
            }
        }

        return $queryData;
    }

    public function populateBookmarks(ContentStoreFile $file, $data)
    {
        $populatedData = [];

        $content = $file->getContent();

        $parser = $this->getParser($file->getMimeType());
        $tokens = $parser->extractTokens($content);

        $bookmarks = $this->getBookmarks($tokens);

        foreach ($bookmarks as $token => $bookmark) {
            if ($bookmark->isStatic()) {

                $result = $bookmark->render();

            } elseif (isset($data[$token])) {

                $bookmark->setData($data[$token]);
                $result = $bookmark->render();

            } else {
                // no data to fulfil this dynamic bookmark, but that's okay
                $result = null;
            }

            if ($result) {
                $populatedData[$token] = $result;
            }
        }

        return $parser->replace($content, $populatedData);
    }

    private function getParser($type)
    {
        $factory = new Parser\ParserFactory();
        return $factory->getParser($type);
    }

    private function getBookmarks($tokens)
    {
        $bookmarks = [];

        $factory = new Bookmark\BookmarkFactory();
        foreach ($tokens as $token) {
            $bookmarks[$token] = $factory->locate($token);
        }

        return $bookmarks;
    }
}
