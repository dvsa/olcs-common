<?php

/**
 * Application Operating Centre Addresses filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Data\Object\Publication as PublicationObject;

/**
 * Application Operating Centre filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationOperatingCentre extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $ocData = $publication->offsetGet('operatingCentreData');
        $newOcData = [];
        $addressFilter = new OperatingCentreAddress();
        $authorisationFilter = new VehicleAuthorisation();

        foreach ($ocData as $oc) {
            $pubObject = new PublicationObject(
                [
                    'operatingCentreAddressData' => $oc['operatingCentre']['address'],
                    'applicationData' => [
                        'totAuthVehicles' => $oc['noOfVehiclesRequired'],
                        'totAuthTrailers' => $oc['noOfTrailersRequired']
                    ],
                    'licType' => $publication->offsetGet('licType')
                ]
            );

            $operatingCentreText = $addressFilter->filter($pubObject)->offsetGet('operatingCentreAddress');
            $authResult = $authorisationFilter->filter($pubObject);

            $authorisationText = '';

            if ($authResult->offsetExists('authorisation')) {
                $authorisationText = $authResult->offsetGet('authorisation');
            }

            switch ($oc['action']) {
                case 'U':
                    $prefix = 'Update ';
                    break;
                case 'D':
                    $prefix = 'Remove ';
                    break;
                default:
                    $prefix = 'New ';
            }

            $newOcData[] = trim($prefix . 'Operating Centre: ' . $operatingCentreText . ' ' . $authorisationText);
        }

        if (!empty($newOcData)) {
            $newData = [
                'operatingCentres' => $newOcData
            ];

            $publication = $this->mergeData($publication, $newData);
        }

        return $publication;
    }
}
