<?php

/**
 * Content store (jackrabbit) file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\File;

use Zend\Http\Response;
use Dvsa\Jackrabbit\Data\Object\File;

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
    public function upload()
    {
        $store = $this->getServiceLocator()->get('ContentStore');
        $file  = $this->getFile();
        $path  = 'documents/' . $this->generateKey();

        if ($file instanceof File) {
            $storeFile = $file;
        } else {
            $storeFile = new File();
            $storeFile->setContent(file_get_contents($file->getPath()))
                ->setMimeType('application/rtf');    // @TODO unstub
        }

        $response = $store->write($path, $storeFile);
        if (!$response->isSuccess()) {
            throw new \Exception('Unable to store uploaded file');
        }

        return $path;
    }

    /**
     * Download the file
     */
    public function download($identifier, $name)
    {
        $store = $this->getServiceLocator()->get('ContentStore');

        $file = $this->getContentStore()->read($identifier);

        $response = new Response();

        if ($file === null) {
            $response->setStatusCode(404);
            $response->setContent('File not found');
            return $response;
        }

        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders(
            array(
                "Content-Type" => $file->getMimeType(),
                "Content-Disposition: attachment; filename=" . $filePath . ".rtf"
            )
        );

        $response->setContent($file->getContent());
        return $response;
    }

    /**
     * Remove the file
     */
    public function remove($identifier)
    {
        // @TODO remove from JR
    }

    private function generateKey()
    {
        return sha1(microtime() . uniqid());
    }
}
