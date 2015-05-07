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
     * @param int        $params['categoryId']      Category ID
     * @param int        $params['subCategoryId']   Sub category ID
     * @param int        $params['entityIdentifier] Entity identifier, for a licence it is the licNo
     * @param int|string $params['description']     Description, if numeric lookup the description
     *
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        // Validate $params
        $requiredParams = ['categoryId', 'subCategoryId', 'entityIdentifier', 'description'];
        foreach ($requiredParams as $paramName) {
            if (!isset($params[$paramName])) {
                $response = new Response(Response::TYPE_FAILED);
                $response->setMessage("'{$paramName}' parameter is missing from the params array.");
                return $response;
            }
        }

        $categoryId = $params['categoryId'];
        $subCategoryId = $params['subCategoryId'];
        $entityIdentifier = $params['entityIdentifier'];
        $description = $params['description'];

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
        // if description is a number, then assume its an id and lookup what the name is
        if (is_numeric($description)) {
            $description = $this->getServiceLocator()->get('DataServiceManager')
                ->get('Olcs\Service\Data\SubCategoryDescription')
                ->setSubCategory($subCategoryId)
                ->getDescriptionFromId($description);
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
}
