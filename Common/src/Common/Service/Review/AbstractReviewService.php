<?php

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Table\Formatter\Address;

/**
 * Abstract Review Service
 *
 * @NOTE Not yet decided whether I should use this abstract to share code, or whether it would be better to use another
 * service, another service would be easier to test in isolation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface
{
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

    protected function formatDate($date)
    {
        return date('d F Y', strtotime($date));
    }

    protected function formatYesNo($value)
    {
        return $value === 'Y' ? 'Yes' : 'No';
    }

    protected function isPsv($data)
    {
        return $data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV;
    }
}
