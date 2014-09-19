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
            $query = $factory->locate($token)->getQuery($data);
            if ($query !== null) {
                $queryData[$token] = $query;
            }
        }

        return $queryData;
    }

    public function populateBookmarks(ContentStoreFile $file, $data)
    {
        $parser = $this->getParser($file->getMimeType());
        $tokens = $parser->extractTokens($file->getContent());

        $populatedData = [];

        $factory = new Bookmark\BookmarkFactory();

        foreach ($tokens as $token) {

            if (!isset($data[$token])) {
                // it's quite possible we don't have any data for a given
                // bookmark in the template; ignore these cases as there's
                // obviously nothing to format
                continue;
            }

            $result = $factory->locate($token)->format($data[$token]);

            if ($result !== null) {
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
