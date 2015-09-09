<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class PeopleSelfServe extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'People';

    /**
     * @var string
     */
    protected $key = 'people';

    /**
     * @var string
     */
    protected $searchIndices = 'person';

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
        if (empty($this->filters)) {

            $this->filters = [];
        }

        return $this->filters;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            ['title' => 'Found As', 'name'=> 'foundAs'],
            [
                'title' => 'Name',
                'formatter' => function ($row) {

                    $name = [

                        $row['personForename'],
                        $row['personFamilyName']
                    ];

                    return implode(' ', $name);
                }
            ],
            [
                'title' => 'Date of Birth',
                'formatter' => function ($row) {

                    //die('<pre>' . print_r($row, 1));

                    return date('d/m/Y', strtotime($row['personBirthDate']));
                }
            ]
        ];
    }
}
