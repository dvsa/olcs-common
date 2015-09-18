<?php

/**
 * Generic Upload
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Traits;

use Dvsa\Olcs\Transfer\Command\Document\CreateDocument;
use Dvsa\Olcs\Transfer\Command\Document\DeleteDocument;
use Dvsa\Olcs\Transfer\Command\Document\Upload;

/**
 * Generic Upload
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait GenericUpload
{
    /**
     * Process files
     *
     * @param \Zend\Form\Form $form
     * @param string $selector - selector identifying the MultipleFileUpload element
     * @param callable $uploadCallback
     * @param callable $deleteCallback
     * @param callable $loadCallback
     * @param string $countSelector - optional selector identifying element to
     * update with number of files uploaded (e.g. for validation)
     * @return bool
     */
    public function processFiles(
        $form,
        $selector,
        $uploadCallback,
        $deleteCallback,
        $loadCallback,
        $countSelector = null
    ) {
        $uploadHelper = $this->getServiceLocator()->get('Helper\FileUpload');

        $uploadHelper->setForm($form)
            ->setSelector($selector)
            ->setUploadCallback($uploadCallback)
            ->setDeleteCallback($deleteCallback)
            ->setLoadCallback($loadCallback)
            ->setRequest($this->getRequest());

        if (!is_null($countSelector)) {
            $uploadHelper->setCountSelector($countSelector);
        }

        return $uploadHelper->process();
    }

    /**
     * Upload a file
     *
     * @param array $fileData
     * @param array $data
     * @return array
     */
    protected function uploadFile($fileData, $data)
    {
        if (!isset($data['filename'])) {
            if (isset($fileData['name'])) {
                $data['filename'] = $fileData['name'];
            } elseif (isset($fileData['filename'])) {
                $data['filename'] = $fileData['filename'];
            }
        }

        if (!isset($fileData['content']) && isset($fileData['tmp_name'])) {
            $fileData['content'] = file_get_contents($fileData['tmp_name']);
        }

        $data['content'] = base64_encode($fileData['content']);

        $response = $this->handleCommand(Upload::create($data));

        return $response->isOk();
    }

    /**
     * Delete file
     *
     * @param int $id
     */
    public function deleteFile($id)
    {
        $response = $this->handleCommand(DeleteDocument::create(['id' => $id]));

        return $response->isOk();
    }
}
