<?php

/**
 * Bus Registration Grant Cancel Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Grant Cancel Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantCancelText3 extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busRegData = $publication->offsetGet('busRegData');

        $text = 'Operating between %s and %s given service number %s effective from %s.';

        $effectiveDate = new \DateTime($busRegData['effectiveDate']);

        $result = sprintf(
            $text,
            $busRegData['startPoint'],
            $busRegData['finishPoint'],
            $publication->offsetGet('busServices'),
            $effectiveDate->format('d F Y')
        );

        $newData = [
            'text3' => $result
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
