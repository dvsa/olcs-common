<?php

/**
 * Bus Registration Publication Section Id filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Publication Section Id filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegPublicationSection extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $previousStatus = $publication->offsetGet('previousStatus');
        $busReg = $publication->offsetGet('busRegData');

        switch ($previousStatus) {
            case 'breg_s_new':
                $section = ($busReg['isShortNotice'] == 'Y' ? 22 : 21);
                break;
            case 'breg_s_var':
                $section = ($busReg['isShortNotice'] == 'Y' ? 24 : 23);
                break;
            case 'breg_s_cancellation':
                $section = ($busReg['isShortNotice'] == 'Y' ? 26 : 25);
                break;
        }

        $newData = [
            'publicationSection' => $section,
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
