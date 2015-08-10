<?php

namespace Common\Data\Object\Search;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class TrafficCommissionerPublications extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Traffic Commissioner Publication';

    /**
     * @var string
     */
    protected $key = 'traffic-commissioner-publication';

    /**
     * @var string
     */
    protected $searchIndices = 'publication';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            ['title' => 'Forename', 'name'=> 'pubNo'],
            ['title' => 'Family name', 'name'=> 'pubSecDesc'],
        ];
    }
}
