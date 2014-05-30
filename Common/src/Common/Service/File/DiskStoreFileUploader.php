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
    public function upload()
    {
        $file = $this->getFile();
        $key = $this->generateKey();
        $location = $this->getConfig()['location'];

        $newPath = rtrim($location, '/') . '/' . $key;

        if (!move_uploaded_file($file->getPath(), $newPath)) {
            throw new \Exception('Unable to move uploaded file');
        } else {
            $file->setPath($newPath);
            $file->setIdentifier($key);
        }
    }

    /**
     * Download the file
     */
    public function download($identifier, $name)
    {
        $location = $this->getConfig()['location'];

        $path = rtrim($location, '/') . '/' . $identifier;

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

        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $name . '"',
                'Content-Tyoe' => 'application/octet-stream',
                'Content-Length' => filesize($path)
            )
        );

        $response->setHeaders($headers);
        return $response;
    }

    /**
     * Remove the file
     */
    public function remove($identifier)
    {
        $location = $this->getConfig()['location'];

        $path = rtrim($location, '/') . '/' . $identifier;

        return unlink($path);
    }

    /**
     * Generate a random sha
     *
     * @return string
     */
    private function generateKey()
    {
        return sha1(microtime() . uniqid());
    }
}
