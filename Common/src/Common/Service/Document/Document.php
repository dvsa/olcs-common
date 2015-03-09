<?php

namespace Common\Service\Document;

use Common\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

/**
 * Document generation service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Document implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const DOCUMENT_TIMESTAMP_FORMAT = 'YmdHi';
    const METADATA_KEY = 'data';

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

            /**
             * Let the bookmark now what parser is currently active;
             * some may use this for sub-bookmark processing
             */
            $bookmark->setParser($parser);

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
                $populatedData[$token] = [
                    'content' => $result,
                    'preformatted' => $bookmark->isPreformatted()
                ];
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
            $bookmark = $factory->locate($token);

            if ($bookmark instanceof DateHelperAwareInterface) {
                $bookmark->setDateHelper(
                    $this->getServiceLocator()->get('Helper\Date')
                );
            }

            $bookmarks[$token] = $bookmark;
        }

        return $bookmarks;
    }

    /**
     * @param $id
     * @param $filename
     * @param $path
     * @return mixed
     */
    public function download($id, $filename, $path = self::TMP_STORAGE_PATH)
    {
        return $this->getUploader()->download($id, $filename, $path);
    }

    /**
     * Returns the METADATA_KEY constant
     *
     * @return string
     */
    public function getMetadataKey()
    {
        return self::METADATA_KEY;
    }

    /**
     * Returns a document timestamp
     *
     * @return string
     */
    public function getTimestampFormat()
    {
        return self::DOCUMENT_TIMESTAMP_FORMAT;
    }

    /**
     * Formats a document filename
     *
     * @param string $input
     * @return string
     */
    public function formatFilename($input)
    {
        return str_replace([' ', '/'], '_', $input);
    }

    /**
     * Get uploader
     *
     * @return \Common\Service\File\FileUploaderFactory
     */
    protected function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader')->getUploader();
    }
}
