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

        $filename = str_replace(" ", "_", $filename);

        $filePath = $this->getServiceLocator()
            ->get('Helper\Date')
            ->getDate('YmdHi') . '_' . $filename . '.rtf';

        return $uploader->upload($folder, $filePath);
    }

    private function getTemplate($template)
    {
        if (!isset($this->templates[$template])) {
            $this->templates[$template] = $this->getServiceLocator()
                ->get('ContentStore')
                ->read('/templates/' . $template . '.rtf');
        }

        return $this->templates[$template];
    }
}
