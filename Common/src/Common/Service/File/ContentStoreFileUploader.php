<?php

/**
 * Content store (jackrabbit) file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\File;

use Zend\Http\Response;
use Dvsa\Jackrabbit\Data\Object\File as ContentStoreFile;

if (!class_exists('Dvsa\Jackrabbit\Data\Object\File')) {
    //@TODO remove this when all applications use version 2.
    //handle BC break in jackrabbit v2.0.0 library
    class_alias('Dvsa\Jackrabbit\Client\Data\Object\File', 'Dvsa\Jackrabbit\Data\Object\File');
}

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
            $this->populateFile();
        }

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent())
            ->setMimeType($file->getRealType())
            ->setMetaData(new \ArrayObject($file->getMeta()));

        $response = $this->getServiceLocator()
            ->get('ContentStore')
            ->write($path, $storeFile);

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

        // @todo DOCUMENT STORE Remove this line when we migrate to the new content store as it doesn't bother saving
        // mimetype, so this is null
        $headers['Content-Type'] = $file->getMimeType();
        $headers['Content-Length'] = strlen($fileData);

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
