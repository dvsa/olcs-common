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
}
