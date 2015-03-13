<?php

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Table\Formatter\Address;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Review Service
 *
 * @NOTE Not yet decided whether I should use this abstract to share code, or whether it would be better to use another
 * service, another service would be easier to test in isolation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected function findFiles($files, $category, $subCategory)
    {
        $foundFiles = [];

        foreach ($files as $file) {
            if ($file['category']['id'] == $category && $file['subCategory']['id'] == $subCategory) {
                $foundFiles[] = $file;
            }
        }

        return $foundFiles;
    }

    protected function formatNumber($number)
    {
        return number_format($number);
    }

    protected function formatAmount($amount)
    {
        return '£' . number_format($amount, 0);
    }

    protected function formatRefdata($refData)
    {
        return $refData['description'];
    }

    protected function formatShortAddress($address)
    {
        return Address::format($address);
    }

    protected function formatFullAddress($address)
    {
        return Address::format($address, ['addressFields' => 'FULL']);
    }

    protected function formatConfirmed($value)
    {
        return $value === 'Y' ? 'Confirmed' : 'Unconfirmed';
    }

    protected function formatDate($date, $format = 'd F Y')
    {
        return date($format, strtotime($date));
    }

    protected function formatYesNo($value)
    {
        return $value === 'Y' ? 'Yes' : 'No';
    }

    protected function isPsv($data)
    {
        return $data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV;
    }

    protected function translate($string)
    {
        return $this->getServiceLocator()->get('Helper\Translation')->translate($string);
    }
}
