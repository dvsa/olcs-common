<?php

/**
 * File controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * File controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileController extends AbstractActionController
{
    /**
     * Download a file
     */
    public function downloadAction()
    {
        $fileUploader = $this->getServiceLocator()->get('FileUploader')->getUploader();
        return $fileUploader->download(
            $this->params()->fromRoute('file'),
            $this->params()->fromRoute('name')
        );
    }
}
