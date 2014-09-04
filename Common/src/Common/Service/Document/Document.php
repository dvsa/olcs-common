<?php

namespace Common\Service\Document;

use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

class Document
{
    private $contentStore;

    public function setContentStore($contentStore)
    {
        $this->contentStore = $contentStore;
    }

    public function getContentStore()
    {
        return $this->contentStore;
    }

    public function generateFromTemplate($templateName, $bookmarks)
    {
        $file = $this->getContentStore()->read($templateName);
        if ($file === null) {
            throw new \Exception('handle properly'); // @TODO
        }
        $generator = $this->getGenerator($file->getMimeType());

        $contents = $generator->generate($file->getContent(), $bookmarks);

        $result = new ContentStoreFile();
        $result->setContent($contents);
        $result->setMimeType($file->getMimeType());

        return $result;
    }

    public function getGenerator($mime)
    {
        switch ($mime) {
        case 'application/rtf':
        case 'application/x-rtf':
            return new RtfGenerator();
        default:
            throw new RuntimeException('No generator found for mime type: ' . $mimeType);
        }
    }
}
