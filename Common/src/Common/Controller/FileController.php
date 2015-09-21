<?php

/**
 * File controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Dvsa\Olcs\Transfer\Query\Document\Download;
use Common\Service\Cqrs\Response;

/**
 * File controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * Download a file
     */
    public function downloadAction()
    {
        $identifier = base64_decode($this->params()->fromRoute('identifier'));

        /** @var Response $downloadResponse */
        $downloadResponse = $this->handleQuery(Download::create(['identifier' => $identifier]));

        if ($downloadResponse->isNotFound()) {
            return $this->notFoundAction();
        }

        if (!$downloadResponse->isOk()) {
            throw new \RuntimeException('Error downloading file');
        }

        $result = $downloadResponse->getResult();

        $download = !$this->params()->fromQuery('inline', false);

        $response = new \Zend\Http\Response();

        if ($download && $this->forceDownload($result['fileName'])) {
            $headers = ['Content-Disposition: attachment; filename="' . $result['fileName'] . '"'];
        }

        $content = base64_decode($result['content']);

        $headers['Content-Length'] = strlen($content);

        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders($headers);

        $response->setContent($content);

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
