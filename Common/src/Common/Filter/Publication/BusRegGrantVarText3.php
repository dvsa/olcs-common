<?php

/**
 * Bus Registration Grant Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Grant Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantVarText3 extends AbstractPublicationFilter
{
    protected $dateFormat = 'd F Y';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busRegData = $publication->offsetGet('busRegData');

        $text = 'Operating between %s and %s given service number %s effective from %s.';

        $variationText =  ' To amend %s.';

        $effectiveDate = new \DateTime($busRegData['effectiveDate']);
        $variationReasons = $publication->offsetGet('variationReasons');

        $result = sprintf(
            $text,
            $busRegData['startPoint'],
            $busRegData['finishPoint'],
            $publication->offsetGet('busServices'),
            $effectiveDate->format($this->dateFormat)
        );

        if ($variationReasons) {
            $result .= sprintf($variationText, $variationReasons);
        }

        $newData = [
            'text3' => $result
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
