<?php

/**
 * Application Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakingsReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        print '<pre>';
        print_r($data);
        exit;

        $subSections = [];



        return [
            'subSections' => $subSections
        ];
    }
}
