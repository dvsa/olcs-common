<?php

namespace CommonTest\Common\Controller\Traits\Stubs;

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

    public function callUploadFile(array $fileData, array $data): bool
    {
        return $this->uploadFile($fileData, $data);
    }

    public function callDeleteFile(int $id): bool
    {
        return $this->deleteFile($id);
    }

    public function handleCommand(\Dvsa\Olcs\Transfer\Command\Document\Upload|\Dvsa\Olcs\Transfer\Command\Document\DeleteDocument $dto)
    {
        $this->stubResponse->dto = $dto;

        return $this->stubResponse;
    }
}
