<?php

namespace Common\Controller;

use Dvsa\Olcs\Transfer\Query\Document\Download;
use Dvsa\Olcs\Transfer\Query\Document\DownloadGuide;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;

/**
 * File controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileController extends ZendAbstractActionController
{
    /**
     * Download a file
     *
     * @return Response\Stream|\Zend\View\Model\ViewModel
     */
    public function downloadAction()
    {
        $identifier = $this->params()->fromRoute('identifier');
        $isInline = (bool)$this->params()->fromQuery('inline');

        if (is_numeric($identifier)) {
            $query = Download::create(
                [
                    'identifier' => $identifier,
                    'isInline' => $isInline,
                ]
            );

        } else {
            // if not a number then we assume it must be a guide document
            $query = DownloadGuide::create(
                [
                    'identifier' => base64_decode($identifier),
                    'isInline' => $isInline,
                ]
            );
        }

        /** @var \Common\Service\Cqrs\Response $downloadResponse */
        $downloadResponse = $this->handleQuery($query);

        if ($downloadResponse->isNotFound()) {
            return $this->notFoundAction();
        }

        if (!$downloadResponse->isOk()) {
            throw new \RuntimeException('Error downloading file');
        }

        $response = $downloadResponse->getHttpResponse();

        // Construct a new response from the one from api
        $newResponse = new Response();
        $newResponse->setContent($response->getContent());

        $headers = new \Zend\Http\Headers();
        $newResponse->setContent($response->getContent());
        $headers->addHeader($response->getHeaders()->get('contentlength'));
        $headers->addHeader($response->getHeaders()->get('contentdisposition'));
        $headers->addHeader($response->getHeaders()->get('contenttype'));
        $newResponse->setHeaders($headers);

        return $newResponse;
    }
}
