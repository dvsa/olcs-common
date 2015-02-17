<?php

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface
{
    protected $sectionName = '';
    protected $lva = '';

    /**
     *
     * @param array $data
     * @return string
     */
    public function getHeader(array $data = array())
    {
        return $this->lva . '-review-' . $this->sectionName;
    }
}
