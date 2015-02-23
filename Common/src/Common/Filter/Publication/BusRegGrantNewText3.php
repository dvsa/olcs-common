<?php

/**
 * Bus Registration Grant New Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Grant New Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantNewText3 extends AbstractPublicationFilter
{
    protected $from = 'From: %s';
    protected $to = 'To: %s';
    protected $via = 'Via: %s';
    protected $serviceDesignation = 'Name or No.: %s';
    protected $serviceType = 'Service type: %s';
    protected $effectiveDate = 'Effective date: %s';
    protected $endDate = 'End date: %s';
    protected $otherDetails = 'Other details: %s';
    protected $dateFormat = 'd F Y';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busRegData = $publication->offsetGet('busRegData');

        $parts = [];

        $parts[] = sprintf($this->from, $busRegData['startPoint']);
        $parts[] = sprintf($this->to, $busRegData['finishPoint']);
        $parts[] = sprintf($this->via, $busRegData['via']);
        $parts[] = sprintf($this->serviceDesignation, $publication->offsetGet('busServices'));
        $parts[] = sprintf($this->serviceType, $publication->offsetGet('busServiceTypes'));

        $effectiveDate = new \DateTime($busRegData['effectiveDate']);
        $parts[] = sprintf($this->effectiveDate, $effectiveDate->format($this->dateFormat));

        if (!is_null($busRegData['endDate'])) {
            $endDate = new \DateTime($busRegData['endDate']);
            $parts[] = sprintf($this->endDate, $endDate->format($this->dateFormat));
        }

        $parts[] = sprintf($this->otherDetails, $busRegData['otherDetails']);

        $newData = [
            'text3' => implode("\n", $parts)
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
