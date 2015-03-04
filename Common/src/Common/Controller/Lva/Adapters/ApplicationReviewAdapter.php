<?php

/**
 * Application Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Application Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationReviewAdapter extends AbstractReviewAdapter
{
    protected $lva = 'application';

    /**
     * Sections to remove during filterSections
     *
     * @var array
     */
    protected $sectionsToIgnore = [
        'community_licences'
    ];
}
