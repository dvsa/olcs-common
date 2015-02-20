<?php

/**
 * Bus Registration Variation Reasons filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Variation Reasons filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegVarReason extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $busReg = $publication->offsetGet('busRegData');

        $reasons = [];

        if (!empty($busReg['variationReasons'])) {
            foreach($busReg['variationReasons'] as $reason) {
                $reasons[] = $reason['description'];
            }
        }

        $numReasons = count($reasons);

        switch ($numReasons) {
            case 0:
                $variationReasons = null;
                break;
            case 1:
                $variationReasons = $reasons[0];
                break;
            default:
                $variationReasons = $reasons[0];

               for ($i = 1; $i < $numReasons; $i++) {
                    if ($i == ($numReasons - 1)) {
                        //array counts from zero, so this is last record
                        $variationReasons .= ' and ' . $reasons[$i];
                    } else {
                        $variationReasons .= ', ' . $reasons[$i];
                    }
                }
        }

        $newData = [
            'variationReasons' => $variationReasons
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
