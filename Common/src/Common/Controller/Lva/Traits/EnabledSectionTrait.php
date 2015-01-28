<?php

/**
 * Enabled Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Service\Entity\ApplicationCompletionEntityService;

/**
 * Enabled Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait EnabledSectionTrait
{
    /**
     * Set the enabled flag
     *
     * @param array $accessibleSections
     * @param array $applicationCompletion
     * @return array
     */
    protected function setEnabledAndCompleteFlagOnSections($accessibleSections, $applicationCompletion)
    {
        $restrictionHelper = $this->getServiceLocator()->get('Helper\Restriction');
        $filter = $this->getServiceLocator()->get('Helper\String');
        $sections = array();
        $completeSections = array();

        foreach ($applicationCompletion as $section => $status) {
            if ($status === ApplicationCompletionEntityService::STATUS_COMPLETE) {
                $section = str_replace('Status', '', $section);
                $completeSections[] = $filter->camelToUnderscore($section);
            }
        }

        foreach ($accessibleSections as $section => $settings) {
            $enabled = true;

            if (isset($settings['prerequisite'])) {
                // ignore any prerequisites that are inaccessible
                $settings['prerequisite'] = $this->removeInaccessible(
                    $settings['prerequisite'],
                    $accessibleSections
                );
            }

            if (!empty($settings['prerequisite'])) {
                $enabled = $restrictionHelper->isRestrictionSatisfied($settings['prerequisite'], $completeSections);
            }

            $complete = in_array($section, $completeSections);

            $sections[$section] = array(
                'enabled'  => $enabled,
                'complete' => $complete
            );
        }

        return $sections;
    }

    protected function removeInaccessible($prerequisites, $accessibleSections)
    {
        if (is_string($prerequisites)) {
            if (!in_array($prerequisites, array_keys($accessibleSections))) {
                return null;
            }
        } elseif (is_array($prerequisites)) {
            $keep = [];
            foreach ($prerequisites as $prerequisite) {
                // recursively handle nested arrays
                if (is_array($prerequisite)) {
                    return array($this->removeInaccessible($prerequisite, $accessibleSections));
                }
                if (in_array($prerequisite, array_keys($accessibleSections))) {
                    $keep[] = $prerequisite;
                }
            }
            return $keep;
        }
    }
}
