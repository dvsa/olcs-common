<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\FinancialEvidenceAdapterInterface;
use Interop\Container\ContainerInterface;

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceAdapter extends AbstractControllerAwareAdapter implements
    FinancialEvidenceAdapterInterface
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Get documents from application data
     *
     * @param int $id Lva object identifier
     *
     * @return array
     */
    abstract public function getDocuments($id);

    /**
     * Prepare Meta data for uploading file
     *
     * @param array $file File post data
     * @param int   $id   Lva object identifier
     *
     * @return array
     */
    abstract public function getUploadMetaData($file, $id);

    /**
     * Alter Form
     *
     * @param \Common\Form\Form $form Form
     *
     * @return void
     */
    abstract public function alterFormForLva($form);

    /**
     * Get Lva subject data
     *
     * @param int  $applicationId Lva object identifier
     * @param bool $noCache       True if need fresh data
     *
     * @return array
     */
    abstract public function getData($applicationId, $noCache = false);
}
