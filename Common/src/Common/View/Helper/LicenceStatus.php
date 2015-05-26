<?php

/**
 * LicenceStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Common\Service\Entity\LicenceEntityService;

/**
 * LicenceStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceStatus extends AbstractHelper
{
    /**
     * Get the HTML to render a licence status
     *
     * @param string $status A licence status ID
     *
     * @return string HTML
     */
    public function __invoke($status)
    {
        return sprintf(
            '<span class="status %s">%s</span>',
            $this->getColourForStatus($status),
            $this->getView()->translate($status)
        );
    }

    /**
     * Get the color class to use
     *
     * @param string $status A licence status ID
     *
     * @return string Class name
     */
    protected function getColourForStatus($status)
    {
        $colors = [
            LicenceEntityService::LICENCE_STATUS_VALID => 'green',
            LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION => 'orange',
            LicenceEntityService::LICENCE_STATUS_GRANTED => 'orange',
            LicenceEntityService::LICENCE_STATUS_SUSPENDED => 'orange',
            LicenceEntityService::LICENCE_STATUS_CURTAILED => 'orange',
            LicenceEntityService::LICENCE_STATUS_WITHDRAWN => 'red',
            LicenceEntityService::LICENCE_STATUS_REFUSED => 'red',
            LicenceEntityService::LICENCE_STATUS_NOT_TAKEN_UP => 'red',
            LicenceEntityService::LICENCE_STATUS_SURRENDERED => 'red',
            LicenceEntityService::LICENCE_STATUS_REVOKED => 'red',
            LicenceEntityService::LICENCE_STATUS_TERMINATED => 'red',
            LicenceEntityService::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT => 'red',
        ];

        if (isset($colors[$status])) {
            return $colors[$status];
        }

        return 'grey';
    }
}
