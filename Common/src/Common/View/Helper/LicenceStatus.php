<?php

/**
 * LicenceStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Common\RefData;

/**
 * LicenceStatus view helper
 *
 * @todo maybe rename this as it is now used elsewhere other than licence
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
        if (is_array($status) && isset($status['colour'])) {
            return $this->render($status['value'], $status['colour']);
        }

        return $this->render($status, $this->getColourForStatus($status));
    }

    protected function render($value, $colour)
    {
        return sprintf(
            '<span class="status %s">%s</span>',
            $colour,
            $this->getView()->translate($value)
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
            RefData::LICENCE_STATUS_VALID => 'green',
            RefData::LICENCE_STATUS_SUSPENDED => 'orange',
            RefData::LICENCE_STATUS_CURTAILED => 'orange',
            RefData::LICENCE_STATUS_SURRENDERED => 'red',
            RefData::LICENCE_STATUS_REVOKED => 'red',
            RefData::LICENCE_STATUS_TERMINATED => 'red',
            RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT => 'red',
            RefData::LICENCE_STATUS_UNDER_CONSIDERATION => 'orange',
            RefData::LICENCE_STATUS_GRANTED => 'orange',
            RefData::LICENCE_STATUS_WITHDRAWN => 'red',
            RefData::LICENCE_STATUS_REFUSED => 'red',
            RefData::LICENCE_STATUS_NOT_TAKEN_UP => 'red',
            // Application statuses @todo double check these colours
            RefData::APPLICATION_STATUS_NOT_SUBMITTED => 'grey',
            RefData::APPLICATION_STATUS_GRANTED => 'green',
            RefData::APPLICATION_STATUS_UNDER_CONSIDERATION => 'orange',
            RefData::APPLICATION_STATUS_VALID => 'green',
            RefData::APPLICATION_STATUS_WITHDRAWN => 'red',
            RefData::APPLICATION_STATUS_REFUSED => 'red',
            RefData::APPLICATION_STATUS_NOT_TAKEN_UP => 'red',
        ];

        if (isset($colors[$status])) {
            return $colors[$status];
        }

        return 'grey';
    }
}
