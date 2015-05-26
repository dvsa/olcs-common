<?php

/**
 * Application Text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Application Text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText1 extends AbstractPublicationFilter
{
    protected $previousPublication = '(Previous Publication:(%s))';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');

        $text = $licenceData['licNo'].$licenceData['licenceType']['olbsKey'];

        //previous publication
        if ($publication->offsetExists('previousPublication')) {
            $text .= ' ' . $this->getPreviousPublication($publication->offsetGet('previousPublication'));
        }

        $newData = [
            'text1' => $text
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }

    /**
     * @param array $previousPublication
     * @return string
     */
    public function getPreviousPublication($previousPublication)
    {
        return sprintf($this->previousPublication, $previousPublication);
    }
}
