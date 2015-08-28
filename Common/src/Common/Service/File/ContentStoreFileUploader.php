<?php

/**
 * Content store file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\File;

use Zend\Http\Response;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;

/**
 * Content store file uploader
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
            $this->populateFile();
        }

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent());

        $response = $this->getServiceLocator()->get('ContentStore')->write($path, $storeFile);

        if (!$response->isSuccess()) {
            throw new Exception('Unable to store uploaded file: ' . $response->getBody());
        }

        $file->setPath($path);
        $file->setIdentifier($key);

        return $file;
    }

    /**
     * Download the file
     */
    public function download($identifier, $name, $namespace = null, $download = true)
    {
        $path = $this->getPath($identifier, $namespace);

        $store = $this->getServiceLocator()->get('ContentStore');

        $file = $store->read($path);

        return $this->serveFile($file, $name, $download);
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

    public function serveFile($file, $name, $download = true)
    {
        $response = new Response();

        if ($file === null) {
            $response->setStatusCode(404);
            $response->setContent('File not found');
            return $response;
        }

        $fileData = $file->getContent();

        if ($download && $this->forceDownload($name)) {
            $headers = ['Content-Disposition: attachment; filename="' . $name . '"'];
        }

        $headers['Content-Length'] = strlen($fileData);
        $headers['Content-Type'] = $file->getRealType();

        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders($headers);

        $response->setContent($fileData);
        return $response;
    }

    protected function forceDownload($name)
    {
        if (preg_match('/\.html$/', $name)) {
            return false;
        }

        return true;
    }
}
