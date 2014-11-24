<?php

/**
 * Previous hearing filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Previous hearing filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousHearing extends AbstractPublicationFilter
{
    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $params = [
            'pi' => $publication->offsetGet('pi'),
            'limit' => 1000
        ];

        $previousHearings = [];
        $hearingData = $publication->offsetGet('hearingData');

        $data = $this->getServiceLocator()
            ->get('DataServiceManager')
            ->get('\Common\Service\Data\PiHearing')
            ->fetchPiHearingData($params);

        //not possible to get what we need from the current API,
        //but there will never be more than a few records to sort through
        foreach ($data['Results'] as $record) {
            $oldHearingDate = new \DateTime($record['hearingDate']);
            $compareDate = $oldHearingDate->format('Y-m-d H:i:s');

            if ($compareDate < $hearingData['hearingDate']) {
                $previousHearings[$compareDate] = [
                    'isAdjourned' => $record['isAdjourned'] == 'Y' ? true : false,
                    'isCancelled' => $record['isCancelled'] == 'Y' ? true : false,
                    'date' => $oldHearingDate->format('d F Y')
                ];
            }
        }

        if (!empty($previousHearings)) {
            krsort($previousHearings);
            $hearingData['previousHearing'] = reset($previousHearings);
            $publication->offsetSet('hearingData', $hearingData);
        }

        return $publication;
    }
}
