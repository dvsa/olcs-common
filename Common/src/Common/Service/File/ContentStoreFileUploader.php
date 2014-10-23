<?php

/**
 * Content store (jackrabbit) file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\File;

use Zend\Http\Response;
use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

/**
 * Content store (jackrabbit) file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ContentStoreFileUploader extends AbstractFileUploader
{
    /**
     * Upload the file
     */
    public function upload($namespace = null, $key = null)
    {
        $file = $this->getFile();

        if ($key === null) {
            $key = $this->generateKey();
        }

        $path = $this->getPath($key, $namespace);

        // allow for the fact the file might already have
        // content set so we won't need to read from tmp disk
        if ($file->getContent() === null) {
            $file->setContent(
                $this->readFile($file)
            );
        }

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent())
            ->setMimeType($file->getRealType())
            ->setMetaData(new \ArrayObject($file->getMeta()));

        $response = $this->getServiceLocator()
            ->get('ContentStore')
            ->write($path, $storeFile);

        if (!$response->isSuccess()) {
            throw new Exception('Unable to store uploaded file');
        }

        $file->setPath($path);
        $file->setIdentifier($key);

        return $file;
    }

    /**
     * Download the file
     */
    public function download($identifier, $name, $namespace = null)
    {
        $path = $this->getPath($identifier, $namespace);

        $store = $this->getServiceLocator()->get('ContentStore');

        $file = $store->read($path);

        $response = new Response();

        if ($file === null) {
            $response->setStatusCode(404);
            $response->setContent('File not found');
            return $response;
        }

        $fileData = $file->getContent();

        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders(
            array(
                'Content-Disposition: attachment; filename="' . $name . '"',
                'Content-Type' => $file->getMimeType(),
                'Content-Length' => strlen($fileData)
            )
        );

        $response->setContent($fileData);
        return $response;
    }

    /**
     * Remove the file
     */
    public function remove($identifier, $namespace = null)
    {
        $path = $this->getPath($identifier, $namespace);
        $store = $this->getServiceLocator()->get('ContentStore');
        return $store->remove($path);
    }

    private function readFile($file)
    {
        return file_get_contents($file->getPath());
    }
}
