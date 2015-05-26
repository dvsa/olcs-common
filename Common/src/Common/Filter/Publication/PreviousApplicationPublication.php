<?php

/**
 * Previous application publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Previous application publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousApplicationPublication extends PreviousPublication
{
    public function filter($publication) {
        $applicationData = $publication->offsetGet('applicationData');

        //if status is new then we don't check for previous
        if ($applicationData['status']['id'] == self::APP_NEW_STATUS) {
            return $publication;
        }

        return parent::filter($publication);
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @param array $previousPublications
     * @return \Common\Data\Object\Publication
     */
    public function setPreviousPublication($publication, $previousPublications)
    {
        $publication->offsetSet('previousPublication', $previousPublications);

        return $publication;
    }

    /**
     * Gets parameters needed to check whether application appears in a previous publication. Checks by licence id or
     * application id, depending on the application status
     *
     * @param $publication
     * @return array
     */
    public function getParams($publication)
    {
        $params = [
            'pubType' => $publication->offsetGet('pubType'),
            'trafficArea' => $publication->offsetGet('trafficArea'),
            'limit' => 'all'
        ];

        $applicationData = $publication->offsetGet('applicationData');

        $checkByLicence = [
            self::APP_GRANTED_STATUS,
            self::APP_REFUSED_STATUS,
            self::APP_NTU_STATUS,
            self::APP_CURTAILED_STATUS
        ];

        //these statuses we check by licence id
        if (in_array($applicationData['status']['id'], $checkByLicence)) {
            $params['licence'] = $publication->offsetGet('licence');
        } else {
            $params['application'] = $publication->offsetGet('application');
        }

        return $params;
    }
}
