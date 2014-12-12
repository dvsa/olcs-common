<?php

/**
 * Police data filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Police data filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PoliceData extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');
        $personData = $licenceData['organisation']['organisationPersons'];

        $persons = [];

        foreach ($personData as $person) {
            $persons[] = [
                'forename' => $person['person']['forename'],
                'familyName' => $person['person']['familyName'],
                'birthDate' => $person['person']['birthDate']
            ];
        }

        $publication->offsetSet('policeData', $persons);

        return $publication;
    }
}
