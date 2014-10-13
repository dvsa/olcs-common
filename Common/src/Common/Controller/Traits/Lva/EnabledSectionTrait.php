<?php

/**
 * Enabled Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Entity\ApplicationCompletionService;

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
    protected function setEnabledFlagOnSections($accessibleSections, $applicationCompletion)
    {
        $restrictionHelper = $this->getHelperService('RestrictionHelper');
        $filter = $this->getHelperService('StringHelper');
        $sections = array();
        $completeSections = array();

        foreach ($applicationCompletion as $section => $status) {
            if ($status === ApplicationCompletionService::STATUS_COMPLETE) {
                $section = str_replace('Status', '', $section);
                $completeSections[] = $filter->camelToUnderscore($section);
            }
        }

        foreach ($accessibleSections as $section => $settings) {
            $enabled = true;

            if (isset($settings['prerequisite'])) {
                $enabled = $restrictionHelper->isRestrictionSatisfied($settings['prerequisite'], $completeSections);
            }

            $sections[$section] = array('enabled' => $enabled);
        }

        return $sections;
    }
}
