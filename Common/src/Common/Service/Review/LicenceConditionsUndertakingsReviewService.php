<?php

/**
 * Licence Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Licence Conditions Undertakings Review Service
 *
 * @NOTE There is not such thing as a Licence Review Section, however our external licence lva version of this page is
 * a read only page with identical config to the review service, so it makes sense to re-use this code
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakingsReviewService extends AbstractReviewService
{
    protected $helper;

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $this->helper = $this->getServiceLocator()->get('Review\ConditionsUndertakings');

        list($licConds, $licUnds, $ocConds, $ocUnds) = $this->helper->splitUpConditionsAndUndertakings($data);

        $subSections = array_merge(
            [],
            $this->processLicenceSections($licConds, $licUnds),
            $this->processOcSections($ocConds, $ocUnds)
        );

        return ['subSections' => $subSections];
    }

    private function processLicenceSections($licConds, $licUnds)
    {
        $subSections = [];

        if (!empty($licConds[''])) {
            $subSections[] = $this->helper
                ->formatLicenceSubSection($licConds[''], 'application', 'conditions', 'added');
        }

        if (!empty($licUnds[''])) {
            $subSections[] = $this->helper
                ->formatLicenceSubSection($licUnds[''], 'application', 'undertakings', 'added');
        }

        return $subSections;
    }

    private function processOcSections($ocConds, $ocUnds)
    {
        $subSections = [];

        if (!empty($ocConds[''])) {
            $subSections[] = $this->helper->formatOcSubSection($ocConds[''], 'application', 'conditions', 'added');
        }

        if (!empty($ocUnds[''])) {
            $subSections[] = $this->helper->formatOcSubSection($ocUnds[''], 'application', 'undertakings', 'added');
        }

        return $subSections;
    }
}
