<?php

/**
 * Continuation checklist generate letter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Data\CategoryDataService;
use Common\Service\File\File;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Continuation checklist generate letter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationChecklistReminderGenerateLetter implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Generate a continuation checklist reminder letter
     *
     * @param int $params['continuationDetailId'] ContinuationDetail ID
     *
     * @return Response
     */
    public function process(array $params)
    {
        if (!isset($params['continuationDetailId'])) {
            return new Response(Response::TYPE_FAILED, [], "'continuationDetailId' parameter is missing");
        }
        $continuationDetailId = (int) $params['continuationDetailId'];

        $continuationDetail = $this->getServiceLocator()->get('Entity\ContinuationDetail')
            ->getDetailsForProcessing($continuationDetailId);

        $template = $this->getTemplateName($continuationDetail);

        try {
            $document = $this->generateChecklist($continuationDetail['licence']['id'], $template);

            if (!($document instanceof File)) {
                return new Response(Response::TYPE_FAILED, [], 'Failed to generate file');
            }
        } catch (\Exception $ex) {
            return new Response(Response::TYPE_FAILED, [], 'Failed to generate file - '. $ex->getMessage());
        }
        try {
            $this->getServiceLocator()->get('Helper\DocumentDispatch')->process(
                $document,
                [
                    'category'    => CategoryDataService::CATEGORY_LICENSING,
                    'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
                    'description' => 'Checklist reminder',
                    'filename'    => $template. '.rtf',
                    'licence'     => $continuationDetail['licence']['id'],
                    'isExternal'  => false,
                    'isScan'  => false,
                ]
            );
        } catch (\Exception $ex) {
            return new Response(Response::TYPE_FAILED, [], 'Failed to dispatch document - '. $ex->getMessage());
        }

        return new Response(Response::TYPE_SUCCESS);
    }

    /**
     * Get the template file name
     *
     * @param array $continuationDetail
     *
     * @return string Template file name
     */
    protected function getTemplateName($continuationDetail)
    {
        $template = 'LIC_CONTD_NO_CHECKLIST_';

        $goodsOrPsv = $continuationDetail['licence']['goodsOrPsv']['id'];
        if ($goodsOrPsv === LicenceEntityService::LICENCE_CATEGORY_PSV) {
            $template .= 'PSV';
        } else {
            $template .= 'GV';
        }

        return $template;
    }

    /**
     * Generate the document
     *
     * @param int    $licenceId Licence ID
     * @param string $template  Template file name
     *
     * @return \Common\Service\File\File
     */
    protected function generateChecklist($licenceId, $template)
    {
        $queryData = [
            'licence' => $licenceId,
            'user' => $this->getServiceLocator()->get('Entity\User')->getCurrentUser()['id'],
        ];
        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateAndStore($template, 'Checklist reminder', $queryData);

        return $storedFile;
    }
}
