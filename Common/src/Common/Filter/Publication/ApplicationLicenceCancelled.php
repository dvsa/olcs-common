<?php

/**
 * Application licence cancelled filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Application licence cancelled filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationLicenceCancelled extends AbstractPublicationFilter
{
    const LIC_TERMINATED = 'Licence terminated WEF ';
    const LIC_SURRENDERED = 'Licence surrendered WEF ';
    const LIC_CNS = 'Licence not continued WEF ';

    /**
     * @var string $date
     */
    protected $date;

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $publicationSection = $publication->offsetGet('publicationSection');

        $newData = [];

        switch ($publicationSection) {
            case self::LIC_SURRENDERED_SECTION:
                $newData['licenceCancelled'] = self::LIC_SURRENDERED . $this->createDate();
                break;
            case self::LIC_TERMINATED_SECTION:
                $newData['licenceCancelled'] = self::LIC_TERMINATED . $this->createDate();
                break;
            case self::LIC_CNS_SECTION:
                $newData['licenceCancelled'] = self::LIC_CNS . $this->createDate();
                break;
        }

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }

    /**
     * Allows easier unit testing
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Gets the date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    public function createDate()
    {
        if ($this->getDate() == null) {
            $dateTime = new \DateTime();
            $this->setDate($dateTime->format('j F Y'));
        }

        return $this->getDate();
    }
}
