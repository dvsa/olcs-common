<?php

/**
 * Access Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Access Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AccessHelperService extends AbstractHelperService
{
    /**
     * Cache the sections
     *
     * @var array
     */
    private $sections;

    /**
     * Setter for sections
     *
     * @param array $sections
     */
    public function setSections(array $sections = array())
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Get sections from section config
     *
     * @return array
     */
    private function getSections()
    {
        return $this->sections;
    }

    /**
     * Get a list of accessible sections
     *
     * @param array $access
     * @return array
     */
    public function getAccessibleSections(array $access = array())
    {
        $sections = $this->getSections();

        foreach (array_keys($sections) as $section) {
            if (!$this->doesHaveAccess($section, $access)) {
                unset($sections[$section]);
            }
        }

        return $sections;
    }

    /**
     * Check if the licence has access to the section
     *
     * @param string $section
     * @param array $access
     * @return boolean
     */
    public function doesHaveAccess($section, array $access = array())
    {
        $sections = $this->getSections();

        $sectionDetails = $sections[$section];

        // If the section has no restrictions just return
        if (!isset($sectionDetails['restricted']) || empty($sectionDetails['restricted'])) {
            return true;
        }

        $restrictions = $sectionDetails['restricted'];

        return $this->getServiceLocator()->get('Helper\Restriction')->isRestrictionSatisfied($restrictions, $access);
    }
}
