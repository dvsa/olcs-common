<?php

/**
 * Transport Manager Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerHelperService extends AbstractHelperService
{
    public function getCertificateFiles($tmId)
    {
        return $this->getServiceLocator()->get('Entity\TransportManager')
            ->getDocuments(
                $tmId,
                null,
                null,
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
            );
    }

    public function getCertificateFileData($tmId, $file)
    {
        return [
            'transportManager' => $tmId,
            'description' => $file['name'],
            'category'    => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        ];
    }
}
