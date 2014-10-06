<?php

/**
 * Section Access Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Service\Data\SectionConfig;

/**
 * Section Access Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionAccessHelperService extends AbstractHelperService
{
    /**
     * Cache the sections
     *
     * @var array
     */
    private $sections;

    /**
     * Get a list of accessible sections
     *
     * @param string $goodsOrPsv
     * @param string $licenceType
     * @return array
     */
    public function getAccessibleSections($goodsOrPsv, $licenceType)
    {
        $sections = $this->getSections();

        foreach (array_keys($sections) as $section) {
            if (!$this->doesLicenceHaveAccess($section, $goodsOrPsv, $licenceType)) {
                unset($sections[$section]);
            }
        }

        return $sections;
    }

    /**
     * Check if the licence has access to the section
     *
     * @param string $section
     * @param string $goodsOrPsv
     * @param string $licenceType
     * @return boolean
     */
    public function doesLicenceHaveAccess($section, $goodsOrPsv, $licenceType)
    {
        $sections = $this->getSections();

        $sectionDetails = $sections[$section];

        // If the section has no restrictions just return
        if (!isset($sectionDetails['restricted'])) {
            return true;
        }

        $access = array($goodsOrPsv, $licenceType);
        $restrictions = $sectionDetails['restricted'];

        return $this->getHelperService('RestrictionHelper')->isRestrictionSatisfied($restrictions, $access);
    }

    /**
     * Get sections from section config
     *
     * @return array
     */
    protected function getSections()
    {
        if ($this->sections === null) {
            $sectionConfig = new SectionConfig();
            $this->sections = $sectionConfig->getAll();
        }

        return $this->sections;
    }
}
