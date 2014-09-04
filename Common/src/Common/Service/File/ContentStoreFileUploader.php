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
    public function upload($namespace = null)
    {
        $identifier = $this->generateKey();

        $store = $this->getServiceLocator()->get('ContentStore');
        $file  = $this->getFile();
        $path  = $namespace. '/' . $identifier;

        // allow for the fact the file might already be a jackrabbit
        // one rather than a tmp uploaded file on disk
        if ($file instanceof ContentStoreFile) {
            $storeFile = $file;
        } else {
            $storeFile = new ContentStoreFile();
            $storeFile->setContent(file_get_contents($file->getPath()))
                ->setMimeType('application/rtf');    // @TODO unstub
        }

        $response = $store->write($path, $storeFile);
        if (!$response->isSuccess()) {
            throw new \Exception('Unable to store uploaded file');
        }

        return $identifier;
    }

    /**
     * Download the file
     */
    public function download($identifier, $name)
    {
        $store = $this->getServiceLocator()->get('ContentStore');

        $file = $store->read($identifier);

        $fileData = $file->getContent();

        $response = new Response();

        if ($file === null) {
            $response->setStatusCode(404);
            $response->setContent('File not found');
            return $response;
        }

        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders(
            array(
                "Content-Disposition: attachment; filename='" . $name . "'",
                "Content-Length" => strlen($fileData)
            )
        );

        $response->setContent($fileData);
        return $response;
    }

    /**
     * Remove the file
     */
    public function remove($identifier)
    {
        $store = $this->getServiceLocator()->get('ContentStore');
        return $store->remove($identifier);
    }
}
