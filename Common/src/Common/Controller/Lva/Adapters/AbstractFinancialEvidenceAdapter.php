<?php

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\FinancialEvidenceAdapterInterface;
use Common\Service\Entity\LicenceEntityService as Licence;

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceAdapter extends AbstractControllerAwareAdapter implements
    FinancialEvidenceAdapterInterface
{
    /**
     * @param int $id
     * @return array
     */
    abstract public function getFormData($id);

    /**
     * @param int $id
     * @return array
     */
    abstract public function getDocuments($id);

    /**
     * @param array $file
     * @param int $id
     * @return array
     */
    abstract public function getUploadMetaData($file, $id);

    /**
     * @param Common\Form\Form
     * @return void
     */
    abstract public function alterFormForLva($form);
}
