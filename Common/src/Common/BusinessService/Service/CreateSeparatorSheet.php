<?php

/**
 * Create and print a scan separator sheet
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;

/**
 * Create and print a scan separator sheet
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateSeparatorSheet implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const CATEGORY_LICENCE = 1;

    const SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS = 74;

    const DESCRIPTION_CHECKLIST = 112;

    /**
     * Create and print a scan separator sheet
     *
     * @param int    $params['categoryId']      Category ID
     * @param int    $params['subCategoryId']   Sub category ID
     * @param int    $params['entityIdentifier] Entity identifier, for a licence it is the licNo
     * @param int    $params['descriptionId']   Description ID (either descriptionId or description must be specified)
     * @param string $params['description']     Description (either descriptionId or description must be specified)
     *
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        if ($message = $this->validateRequiredParams($params) !== null) {
            $response = new Response(Response::TYPE_FAILED);
            $response->setMessage($message);
            return $response;
        }

        $categoryId = (int) $params['categoryId'];
        $subCategoryId = (int) $params['subCategoryId'];
        $entityIdentifier = $params['entityIdentifier'];
        $description = (isset($params['description'])) ? $params['description'] : null;
        $descriptionId = (isset($params['descriptionId'])) ? (int) $params['descriptionId'] : null;

        $processingService = $this->getServiceLocator()->get('Processing\ScanEntity');
        $entity = $processingService->findEntityForCategory($categoryId, $entityIdentifier);
        if ($entity === false) {
            $response = new Response(Response::TYPE_FAILED);
            $response->setMessage("Cannot find entity for category '{$categoryId}'.");
            return $response;
        }

        $licNo = isset($entity['licNo']) ? $entity['licNo'] : 'Unknown';
        $categoryName = $this->getServiceLocator()->get('DataServiceManager')->get('Olcs\Service\Data\Category')
            ->getDescriptionFromId($categoryId);
        $subCategoryName = $this->getServiceLocator()->get('DataServiceManager')->get('Olcs\Service\Data\SubCategory')
            ->setCategory($categoryId)
            ->getDescriptionFromId($subCategoryId);
        // if description ID exists, lookup the description name
        if ($descriptionId !== null) {
            $description = $this->getServiceLocator()->get('DataServiceManager')
                ->get('Olcs\Service\Data\SubCategoryDescription')
                ->setSubCategory($subCategoryId)
                ->getDescriptionFromId($descriptionId);
        }

        $entityType = $processingService->findEntityNameForCategory($categoryId);

        $children = $processingService->getChildrenForCategory($categoryId, $entity);

        $data = array_merge(
            [
                'category' => $categoryId,
                'subCategory' => $subCategoryId,
                // freetext is correct
                'description' => $description
            ],
            $children
        );

        $record = $this->getServiceLocator()->get('Entity\Scan')->save($data);

        $knownValues = [
            'DOC_CATEGORY_ID_SCAN'       => $categoryId,
            'DOC_CATEGORY_NAME_SCAN'     => $categoryName,
            'LICENCE_NUMBER_SCAN'        => $licNo,
            'LICENCE_NUMBER_REPEAT_SCAN' => $licNo,
            'ENTITY_ID_TYPE_SCAN'        => $entityType,
            'ENTITY_ID_SCAN'             => $entity['id'],
            'ENTITY_ID_REPEAT_SCAN'      => $entity['id'],
            'DOC_SUBCATEGORY_ID_SCAN'    => $subCategoryId,
            'DOC_SUBCATEGORY_NAME_SCAN'  => $subCategoryName,
            'DOC_DESCRIPTION_ID_SCAN'    => $record['id'],
            'DOC_DESCRIPTION_NAME_SCAN'  => $description
        ];

        $docService = $this->getServiceLocator()->get('Helper\DocumentGeneration');

        $content = $docService->generateFromTemplate('Scanning_SeparatorSheet', [], $knownValues);

        $storedFile = $docService->uploadGeneratedContent($content, 'documents', 'Scanning Separator Sheet');

        $this->getServiceLocator()->get('PrintScheduler')->enqueueFile($storedFile, 'Scanning Separator Sheet');

        return new Response(Response::TYPE_SUCCESS);
    }

    /**
     * Validate the parameters
     *
     * @param array $params
     *
     * @return string Error message
     */
    protected function validateRequiredParams($params)
    {
        // Validate $params
        $requiredParams = ['categoryId', 'subCategoryId', 'entityIdentifier'];
        foreach ($requiredParams as $paramName) {
            if (!isset($params[$paramName])) {
                return "'{$paramName}' parameter is missing from the params array.";
            }
        }

        if (!isset($params['description']) && !isset($params['descriptionId'])) {
            return "'description' or 'descriptionId' parameter must be specified.";
        }

        return null;
    }
}
