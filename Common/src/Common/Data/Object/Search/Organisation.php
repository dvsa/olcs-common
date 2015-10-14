<?php
namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

/**
 * Class Organisation
 * @package Common\Data\Object\Search
 */
class Organisation extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Organisation';
    /**
     * @var string
     */
    protected $key = 'organisation';

    /**
     * @var string
     */
    protected $searchIndices = 'operator';

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
    public function getSettings()
    {
        return [
            'crud' => [
                'links' => [
                    'create-operator' => [
                        'label' => 'Create operator',
                        'class' => 'primary js-modal-ajax',
                        'route' => [
                            'route' => 'create_operator'
                        ]
                    ],
                    'create-unlicensed-operator' => [
                        'label' => 'Create unlicensed operator',
                        'class' => 'primary js-modal-ajax',
                        'route' => [
                            'route' => 'create_unlicensed_operator'
                        ]
                    ]
                ]
            ],
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data) {

                    $orgName = $data['orgName'];
                    if ($data['noOfLicencesHeld'] > 1) {
                        $orgName .= ' (MLH)';
                    }

                    return '<a href="/operator/' . $data['orgId'] . '">' .$orgName . '</a>';
                }
            ],
            ['title' => 'Trading name', 'name'=> 'tradingName'],

            ['title' => 'Licence number', 'name'=> 'licNo'],
            ['title' => 'Licence type', 'name'=> 'licTypeDesc'],
            ['title' => 'Licence status', 'name'=> 'licStatusDesc'],
            [
                'title' => 'Address',
                'formatter' => function ($row) {

                    return isset($row['address']) ? $row['address'] : '';
                }
            ],
            ['title' => 'Person', 'name'=> 'person'],
        ];
    }
}
