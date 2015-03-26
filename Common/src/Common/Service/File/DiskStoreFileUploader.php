<?php

/**
 * Disk Store File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\File;

use Zend\Http\Response;
use Zend\Http\Response\Stream;
use Zend\Http\Headers;

/**
 * Disk Store File Uploader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DiskStoreFileUploader extends AbstractFileUploader
{
    /**
     * Upload the file
     */
    public function upload($namespace = null, $key = null)
    {
        $file = $this->getFile();
        /**
         * N.B. key is always ignored; this is intentional
         * The Disk Store always controls its own physical
         * representation of a file
         */
        $key = $this->generateKey();

        $newPath = $this->getPath($key, $namespace);

        if (!$this->moveFile($file->getPath(), $newPath)) {
            throw new \Exception('Unable to move uploaded file');
        }

        $file->setPath($newPath);
        $file->setIdentifier($key);

        return $file;
    }

    /**
     * Move file
     *
     * @param string $oldPath
     * @param string $newPath
     */
    public function moveFile($oldPath, $newPath)
    {
        return move_uploaded_file($oldPath, $newPath);
    }

    /**
     * Download the file
     */
    public function download($identifier, $name, $namespace = null, $download = true)
    {
        $path = $this->getPath($identifier, $namespace);

        if (!file_exists($path)) {
            $response = new Response();
            $response->setStatusCode(404);
            $response->setContent('File not found');
            return $response;
        }

        $response = new Stream();
        $response->setStream(fopen($path, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName($name);

        $headers = new Headers();
        $headersArray = [];

        if ($download && $this->forceDownload($name)) {
            $headersArray['Content-Disposition'] = 'attachment; filename="' . $name . '"';
        }

        $headersArray['Content-Type'] = 'application/octet-stream';
        $headersArray['Content-Length'] = filesize($path);

        $headers->addHeaders($headersArray);

        $response->setHeaders($headers);
        return $response;
    }

    protected function forceDownload($name)
    {
        if (preg_match('/\.html$/', $name)) {
            return false;
        }

        return true;
    }

    /**
     * Remove the file
     */
    public function remove($identifier, $namespace = null)
    {
        return unlink($this->getPath($identifier, $namespace));
    }
}
