<?php

namespace Common\Service\Document;

class Document
{
    public function getBookmarkQueries($type, $content, $data)
    {
        $queryData = [];

        $tokens = $this->getParser($type)
            ->extractTokens($content);

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

    public function populateBookmarks($type, $content, $data)
    {
        $populatedData = [];

        $parser = $this->getParser($type);
        $tokens = $parser->extractTokens($content);

        $bookmarks = $this->getBookmarks($tokens);

        foreach ($bookmarks as $token => $bookmark) {
            if ($bookmark->isStatic()) {

                $result = $bookmark->render();

            } else if (isset($data[$token])) {

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
