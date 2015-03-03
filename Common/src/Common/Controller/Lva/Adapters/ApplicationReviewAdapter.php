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
    private $sectionsToIgnore = [
        'community_licences'
    ];

    /**
     * Filter unwanted sections
     *
     * @param int $id
     * @param array $sections
     * @return array
     */
    protected function filterSections($id, $sections)
    {
        return array_diff($sections, $this->sectionsToIgnore);
    }
}
