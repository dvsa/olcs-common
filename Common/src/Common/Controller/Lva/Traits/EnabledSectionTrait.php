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

        $completeCount  = 0;
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

            $completeCount += ($complete ? 1 : 0);
        }

        // Undertakings/Declarations section only enabled once ALL OTHER
        // sections are complete, https://jira.i-env.net/browse/OLCS-2236
        if (array_key_exists('undertakings', $accessibleSections)) {
            $sections['undertakings']['enabled'] = ($completeCount >= (count($accessibleSections)-1));
        }

        return $sections;
    }
}
