<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Data\Object\Search\Aggregations\DateRange as DateRange;

/**
 * Class Publications
 * Used by Selfserve publication search
 *
 * @package Common\Data\Object\Search
 */
class PublicationSelfserve extends InternalSearchAbstract
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
     * Contains an array of the instantiated Date Ranges classes.
     *
     * @var array
     */
    protected $dateRanges = [];

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Contains an array of required results order
     *
     * @var array
     */
    protected $orderOptions = [
        0 => [
            'field' => 'pubDate',
            'field_label' => 'Most recent publication',
            'order' => 'ASC'
        ],
        1 => [
            'field' => 'pubDate',
            'field_label' => 'Oldest publication',
            'order' => 'DESC'
        ],
    ];

    /**
     * Returns an array of date ranges for this index
     *
     * @return array
     */
    public function getDateRanges()
    {
        if (empty($this->dateRanges)) {

            $this->dateRanges = [
                new DateRange\PublishedDateFrom(),
                new DateRange\PublishedDateTo(),
            ];
        }

        return $this->dateRanges;
    }

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {

            $this->filters = [
                new Filter\LicenceType(),
                new Filter\TrafficArea(),
                new Filter\PublicationType(),
                new Filter\DocumentStatus(),
                new Filter\PublicationSection()
            ];
        }

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

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50]
                ]
            ],
            'layout' => 'traffic-commissioner-publication',
        ];
    }
}
