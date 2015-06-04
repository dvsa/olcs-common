<?php

/**
 * Document Generation Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Document Generation Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentGenerationHelperService extends AbstractHelperService
{
    /**
     * Hold an in memory cache of templates fetched from the store;
     * Useful when multiple copies of the same template are printed
     * during a single request
     */
    private $templateCache = [];

    /**
     * Helper method to generate a string of content from a given template and
     * query parameters
     *
     * @param string $template
     * @param array $queryData
     * @param array $knownValues
     *
     * @return string
     */
    public function generateFromTemplate($template, $queryData = [], $knownValues = [])
    {
        $documentService = $this->getServiceLocator()->get('Document');

        $file = $this->getTemplate($template);

        $query = $documentService->getBookmarkQueries($file, $queryData);

        if (!empty($query)) {
            $result = $this->getServiceLocator()
                ->get('Helper\Rest')
                ->makeRestCall('BookmarkSearch', 'GET', [], $query);
        } else {
            $result = [];
        }

        $result = array_merge($result, $knownValues);

        return $documentService->populateBookmarks($file, $result);
    }

    public function uploadGeneratedContent($content, $folder, $filename)
    {
        $uploader = $this->getServiceLocator()
            ->get('FileUploader')
            ->getUploader();

        $uploader->setFile(['content' => $content]);

        /*
         * @NOTE: setting the filepath of the identifier conflicts
         * with the need to store files uniquely (which the uploader
         * will otherwise take care of). As per discussions 13/02/15
         * we've agreed not to set "friendly" file paths and that
         * a separate task is needed to identify a solution
        $filePath = $this->getServiceLocator()
            ->get('Helper\Date')
            ->getDate('YmdHi') . '_' . $filename . '.rtf';
         */

        return $uploader->upload($folder);
    }

    /**
     * Generate and store a document
     *
     * @param string $template    Document template name
     * @param string $description Not used
     * @param array  $queryData
     * @param array  $knownValues
     *
     * @return \Common\Service\File\File
     */
    public function generateAndStore($template, $description, $queryData = [], $knownValues = [])
    {
        $template = $this->addTemplatePrefix($queryData, $template);

        $content = $this->generateFromTemplate($template, $queryData, $knownValues);

        return $this->uploadGeneratedContent($content, 'documents', $description);
    }

    public function addTemplatePrefix($queryData, $template)
    {
        foreach (['application', 'licence'] as $key) {
            if (isset($queryData[$key])) {
                $entity = ucfirst($key);
                $data = $this->getServiceLocator()
                    ->get('Entity\\' . $entity)
                    ->getOverview($queryData[$key]);

                return $this->getPrefix($data) . '/' . $template;
            }
        }

        return $template;
    }

    private function getPrefix(array $licence)
    {
        return $licence['niFlag'] === 'N' ? 'GB' : 'NI';
    }

    private function getTemplate($template)
    {
        if (!isset($this->templateCache[$template])) {
            $this->templateCache[$template] = $this->getServiceLocator()
                ->get('ContentStore')
                ->read('/templates/' . $template . '.rtf');
        }

        return $this->templateCache[$template];
    }
}
