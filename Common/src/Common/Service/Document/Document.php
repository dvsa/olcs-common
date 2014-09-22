<?php

namespace Common\Service\Document;

use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

class Document
{
    public function getBookmarkQueries(ContentStoreFile $file, $data)
    {
        $queryData = [];

        $tokens = $this->getParser($file->getMimeType())
            ->extractTokens($file->getContent());

        $factory = new Bookmark\BookmarkFactory();

        foreach ($tokens as $token) {
            $bookmark = $factory->locate($token);
            if ($bookmark->isStatic()) {
                continue;
            }

            $query = $factory->locate($token)->getQuery($data);
            if ($query !== null) {
                $queryData[$token] = $query;
            }
        }

        return $queryData;
    }

    public function populateBookmarks(ContentStoreFile $file, $data)
    {
        $populatedData = [];

        $parser = $this->getParser($file->getMimeType());
        $tokens = $parser->extractTokens($file->getContent());

        $factory = new Bookmark\BookmarkFactory();

        foreach ($tokens as $token) {
            $bookmark = $factory->locate($token);

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

        $content = $parser->replace($file->getContent(), $populatedData);

        $file->setContent($content);

        return $file;
    }

    private function getParser($mime)
    {
        switch ($mime) {
        case 'application/rtf':
        case 'application/x-rtf':
            return new Parser\RtfParser();
        default:
            throw new RuntimeException('No parser found for mime type: ' . $mimeType);
        }
    }
}
