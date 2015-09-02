<?php

namespace Common\Data\Object\Search;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class People extends InternalSearchAbstract
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
                    'create-transport-manager' => [
                        'label' => 'Create transport manager',
                        'class' => 'primary',
                        'route' => [
                            'route' => 'create_transport_manager'
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
            ['title' => 'Found As', 'name'=> ''],
            ['title' => 'Forename', 'name'=> 'personForename'],
            ['title' => 'Family name', 'name'=> 'personFamilyName'],
            ['title' => 'DOB', 'name'=> 'personBirthDate'],
            ['title' => 'Date added', 'name'=> ''],
            ['title' => 'Date removed', 'name'=> ''],
            ['title' => 'Disq?', 'name'=> '']
        ];
    }
}
