<?php

/**
 * Companies House Api Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\CompaniesHouse;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Common\Util\RestCallTrait;

/**
 * Companies House Api Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Api implements ServiceLocatorAwareInterface
{
    use RestCallTrait;
    use ServiceLocatorAwareTrait;

    public function getCompanyProfile($companyNumber)
    {
        return $this->sendGet('companies_house_rest\\company', [$companyNumber], true);
    }
}
