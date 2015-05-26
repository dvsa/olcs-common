<?php

/**
 * Application bus note filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Application bus note filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationBusNote extends AbstractPublicationFilter
{
    const BUS_STRING = 'Registered Bus Services running under this licence have also been %s with immediate effect.';

    const BUS_REVOKED = 'revoked';
    const BUS_SURRENDERED = 'surrendered';
    const BUS_CNS = 'set to CNS';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licType = $publication->offsetGet('licType');
        $publicationSection = $publication->offsetGet('publicationSection');

        $newData = [];

        if ($licType == self::PSV_LIC_TYPE) {
            switch ($publicationSection) {
                case self::LIC_SURRENDERED_SECTION:
                    $newData['busNote'] = sprintf(self::BUS_STRING, self::BUS_SURRENDERED);
                    break;
                case self::LIC_REVOKED_SECTION:
                    $newData['busNote'] = sprintf(self::BUS_STRING, self::BUS_REVOKED);
                    break;
                case self::LIC_CNS_SECTION:
                    $newData['busNote'] = sprintf(self::BUS_STRING, self::BUS_CNS);
                    break;
            }
        }

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
