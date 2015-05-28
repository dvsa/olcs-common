<?php

/**
 * VehicleAuthorisation filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * VehicleAuthorisation filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class VehicleAuthorisation extends AbstractPublicationFilter
{
    const GV_LIC_TYPE = 'lcat_gv';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licType = $publication->offsetGet('licType');
        $appData = $publication->offsetGet('applicationData');

        $gvWithTrailers = (bool)($licType == self::GV_LIC_TYPE
            && $appData['totAuthTrailers']);

        //check we have authorised vehicles
        if (!$appData['totAuthVehicles'] && !$gvWithTrailers) {
            return $publication;
        }

        $text = 'Authorisation: ';

        if ($appData['totAuthVehicles'] && $gvWithTrailers) {
            $text .= $appData['totAuthVehicles'] . ' Vehicle(s) and ' . $appData['totAuthTrailers'] . ' Trailer(s)';
        } elseif ($appData['totAuthVehicles']) {
            $text .= $appData['totAuthVehicles'] . ' Vehicle(s)';
        } else {
            $text .= $appData['totAuthTrailers'] . ' Trailer(s)';
        }

        $publication->offsetSet('authorisation', $text);

        return $publication;
    }
}
