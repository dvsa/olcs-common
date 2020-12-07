<?php

namespace CommonTest\Controller\Traits\Stubs;

use Common\Controller\Traits\GenericUpload;
use Laminas\Mvc\Controller\AbstractActionController;

/**
 * Generic Upload Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericUploadStub extends AbstractActionController
{
    use GenericUpload;

    public $stubResponse;

    public function callUploadFile($fileData, $data)
    {
        return $this->uploadFile($fileData, $data);
    }

    public function callDeleteFile($id)
    {
        return $this->deleteFile($id);
    }

    public function handleCommand($dto)
    {
        $this->stubResponse->dto = $dto;

        return $this->stubResponse;
    }
}
