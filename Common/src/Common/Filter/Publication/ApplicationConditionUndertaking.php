<?php

/**
 * Application condition undertaking filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Data\Object\Publication as PublicationObject;

/**
 * Application condition undertaking filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationConditionUndertaking extends AbstractPublicationFilter
{
    const ATTACHED_LIC = 'Attached to Licence.';
    const ATTACHED_OC = 'Attached to Operating Centre: %s';

    const COND_NEW = 'New %s: %s';
    const COND_REMOVE = '%s to be removed: %s';
    const COND_UPDATE = 'Current %s: %s';
    const COND_AMENDED = 'Amended to: %s';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $params = [
            'application' => $publication->offsetGet('application'),
            'limit' => 'all'
        ];

        $conditionUndertaking = $this->getServiceLocator()
            ->get('Generic\Service\Data\ConditionUndertaking')
            ->fetchList($params);

        $data = [];

        if (!empty($conditionUndertaking)) {
            $addressFilter = new OperatingCentreAddress();

            foreach ($conditionUndertaking as $key => $result) {
                //work out the action
                switch ($result['action']) {
                    case 'A':
                        $action = self::COND_NEW;
                        break;
                    case 'D':
                        $action = self::COND_REMOVE;
                        break;
                    case 'U':
                        $action = self::COND_UPDATE;
                        break;
                    default:
                        $action = self::COND_NEW;
                }

                $string = sprintf($action, $result['conditionType']['description'], $result['notes']);

                //work out if it's a licence or an oc
                if (!empty($result['operatingCentre'])) {
                    $pubObject = new PublicationObject(
                        ['operatingCentreAddressData' => $result['operatingCentre']['address']]
                    );

                    /* @var \Common\Data\Object\Publication */
                    $formattedAddress = $addressFilter->filter($pubObject);

                    $string .= ' Attached to Operating Centre: '
                        . $formattedAddress->offsetGet('operatingCentreAddress');
                } else {
                    $string .= ' Attached to Licence.';
                }

                if ($result['action'] == 'U') {
                    $string .= " " . sprintf(self::COND_AMENDED, $result['notes']);
                }

                $data[$key] = $string;
            }
        }

        $newData = [
            'conditionUndertaking' => $data
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
