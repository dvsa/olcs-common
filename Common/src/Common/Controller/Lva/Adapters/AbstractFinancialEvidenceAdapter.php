<?php

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\FinancialEvidenceAdapterInterface;
use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Common\Controller\Lva\Traits\ControllerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceAdapter
    extends AbstractAdapter
    implements FinancialEvidenceAdapterInterface
{
    abstract public function getTotalNumberOfAuthorisedVehicles($id);

    abstract public function getRequiredFinance($id);

    public function alterFormForLva($form)
    {
        // no-op, can be extended
    }
}
