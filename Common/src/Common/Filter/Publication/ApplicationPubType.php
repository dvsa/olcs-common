<?php

/**
 * ApplicationPubType filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * ApplicationPubType filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationPubType extends AbstractPublicationFilter
{
    const GV_LIC_TYPE = 'lcat_gv';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $applicationData = $publication->offsetGet('applicationData');

        $publicationType = ($applicationData['goodsOrPsv']['id'] == self::GV_LIC_TYPE ? 'A&D' : 'N&P');

        $newData = [
            'publicationType' => $publicationType
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
