<?php

/**
 * Pagination Helper
 *
 * Formats pagination options
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table;

/**
 * Pagination Helper
 *
 * Formats pagination options
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaginationHelper
{
    /**
     * Current page
     *
     * @var int
     */
    private $page;

    /**
     * Total result count
     *
     * @var int
     */
    private $total;

    /**
     * Limit per page
     *
     * @var int
     */
    private $limit;

    /**
     * Current page deviation
     *
     * @var int
     */
    private $pageDeviation = 2;

    /**
     * Pagination options
     *
     * @var array
     */
    private $options = array();

    /**
     * Pass in the settings
     *
     * @param int $page
     * @param int $total
     * @param int $limit
     */
    public function __construct($page, $total, $limit)
    {
        $this->page = $page;
        $this->total = $total;
        $this->limit = $limit;
    }

    /**
     * Return the formatted options
     *
     * @return array
     */
    public function getOptions()
    {
        $totalPages = ceil($this->total / $this->limit);

        $this->maybeAddPreviousOption();

        $totalPagesToDisplay = ($this->pageDeviation * 2) + 3;

        if ($totalPages <= $totalPagesToDisplay) {

            $this->addRangeOfOptions(1, $totalPages);

        } else {

            $this->addRangeOptions($totalPages);
        }

        $this->maybeAddNextOption($totalPages);

        return $this->options;
    }

    /**
     * Add the range options
     *
     * @param int $totalPages
     */
    private function addRangeOptions($totalPages)
    {
        $this->addPageOption(1);

        if ($totalPages > 1) {

            $lowerRange = $this->calculateLowerRange($totalPages);

            $upperRange = $this->calculateUpperRange($totalPages);

            $this->maybeAddAbbreviationOption(($lowerRange > 2));

            $this->addRangeOfOptions($lowerRange, $upperRange);

            $this->maybeAddAbbreviationOption(($upperRange <= ($totalPages - 2)));

            $this->addPageOption($totalPages);
        }
    }

    /**
     * Add a range of options
     *
     * @param int $lowerRange
     * @param int $upperRange
     */
    private function addRangeOfOptions($lowerRange, $upperRange)
    {
        for ($i = $lowerRange; $i <= $upperRange; $i++) {

            $this->addPageOption($i);
        }
    }

    /**
     * Decide whether to add the "Previous" option
     */
    private function maybeAddPreviousOption()
    {
        if ($this->page > 1) {

            $this->addPageOption(($this->page - 1), 'Previous', false);
        }
    }

    /**
     * Maybe add the Next option
     *
     * @param int $totalPages
     */
    private function maybeAddNextOption($totalPages)
    {
        if ($this->page < $totalPages) {

            $this->addPageOption(($this->page + 1), 'Next', false);
        }
    }

    /**
     * Decide whether to add the lower range ...'s
     *
     * @param boolean $add
     */
    private function maybeAddAbbreviationOption($add)
    {
        if ($add) {

            $this->addPageOption(null, '...', false);
        }
    }
    /**
     * Calculate the lower range
     *
     * @param int $totalPages
     * @return int
     */
    private function calculateLowerRange($totalPages)
    {
        $lowerRangeCheck = ($this->page >= ($totalPages - 2) ? (2 - ($totalPages - $this->page)) : 0);

        return max(
            array(
                2,
                ($this->page - $this->pageDeviation) - $lowerRangeCheck
            )
        );
    }

    /**
     * Calculate upper range
     *
     * @param int $totalPages
     * @return int
     */
    private function calculateUpperRange($totalPages)
    {
        return min(
            array(
                ($totalPages - 1),
                ($this->page + $this->pageDeviation) + ($this->page <= 2 ? (3 - $this->page) : 0)
            )
        );
    }

    /**
     * Add a page option
     *
     * @param int|string $page
     * @param int|string $label
     * @param boolean $hasClass
     */
    private function addPageOption($page, $label = null, $hasClass = true)
    {
        $this->options[] = $this->formatPageOption($page, $label, $hasClass);
    }

    /**
     * Format a page option
     *
     * @param int|string $page
     * @param int|string $label
     * @param boolean $hasClass
     * @return array
     */
    private function formatPageOption($page, $label = null, $hasClass = true)
    {
        $array = array(
            'page' => is_null($page) ? null : (string)$page,
            'label' => (is_null($label) ? (string)$page : $label),
        );

        if ($hasClass) {
            $array['class'] = ($this->page == $page ? 'current' : null);
        }

        return $array;
    }
}
