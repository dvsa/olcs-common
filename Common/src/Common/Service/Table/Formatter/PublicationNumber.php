<?php

/**
 * Publication Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Publication Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationNumber implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['pubStatus']['id'] === 'pub_s_new') {
            return $data['publicationNo'];
        }

        $publicationService = $sm->get('DataServiceManager')->get('Common\Service\Data\Publication');
        $urlParams = $publicationService->getFilePathVariablesFromPublication($data);

        $uploader = $sm->get('FileUploader')->getUploader();
        $documentPath = $uploader->buildPathNamespace($urlParams);

        $uriPattern = $sm->get('Config')['document_share']['uri_pattern'];

        $url = str_replace('/', '\\', sprintf($uriPattern, $documentPath . '/' . $data['document']['filename']));

        return sprintf('<a href="%s" target="blank">%s</a>', $url, $data['publicationNo']);
    }
}
